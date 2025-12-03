<?php

namespace Modules\MainApp\Http\Repositories;

use PDO;
use App\Enums\Status;
use App\Models\Tenant;
use App\Models\User;
use App\Enums\Settings;
use Illuminate\Support\Str;
use App\Enums\PricingDuration;
use App\Enums\SubscriptionStatus;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\MainApp\Entities\School;
use Modules\MainApp\Entities\Package;
use Illuminate\Support\Facades\Session;
use Modules\MainApp\Jobs\SaasSchoolApproveJob;
use Modules\MainApp\Entities\Subscription;
use Modules\MainApp\Services\SaaSSchoolService;
use Modules\MainApp\Services\BranchDataSeederService;
use Modules\MainApp\Http\Interfaces\SchoolInterface;

class SchoolRepository implements SchoolInterface
{
    use ReturnFormatTrait;
    private $model;

    public function __construct(School $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model::all();
    }

    public function activeAll()
    {
        return $this->model::active()->get();
    }

    public function getAll()
    {
        return $this->model->latest()->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        // Use database transaction for atomicity
        DB::beginTransaction();

        try {
            $source = $request->source ?? 'website';
            $request->merge(['package_id' => $request->package]);

            // Create school and all its branches
            $school = $this->storeSchool($request, $source);

            // Create subscription with multi-branch pricing
            $subscription = $this->storeSubscription($request, $school, $payment_method = null);

            if (($source == 'admin')) {
                SaasSchoolApproveJob::dispatch($subscription);
            }

            // Commit transaction if everything succeeds
            DB::commit();

            \Log::info('School created successfully', [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'branches_created' => $request->number_of_branches ?? 1,
                'subscription_id' => $subscription->id
            ]);

            return $this->responseWithSuccess(___('alert.created_successfully_it_will_be_active_soon'), []);
        } catch (\Throwable $th) {
            // Rollback transaction on any error
            DB::rollBack();

            // Log the error with full context
            \Log::error('School store error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->except(['admin_password', 'admin_password_confirmation'])
            ]);

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }


    protected function storeSchool($request, $source)
    {
        // Auto-generate sub_domain_key from school name if not provided
        if (empty($request['sub_domain_key'])) {
            $request['sub_domain_key'] = $this->generateSubDomainKey($request['name']);
        }

        $school = School::where('sub_domain_key', $request['sub_domain_key'])->first();
        if (!$school) {
            $school                    = new School();
            $school->sub_domain_key     = $request['sub_domain_key'];
            $school->name               = $request['name'];
            $school->package_id         = $request['package_id'];
            $school->address            = $request['address'];
            $school->phone              = $request['phone'];
            $school->email              = $request['email'];
            $school->status             = Status::INACTIVE;
            $school->save();

            // Create multiple branches based on user input
            $numberOfBranches = $request->number_of_branches ?? 1;
            $branches = $this->createMultipleBranches($school, $numberOfBranches);

            // Create admin user with the first (main) branch
            $mainBranch = $branches->first();
            $this->createSchoolAdminUser($school, $request, $mainBranch);

            // Seed default data for each branch FIRST (categories, fee types, sessions, etc.)
            // IMPORTANT: Sessions must be created before seedSchoolSettings() runs,
            // otherwise the 'session' setting will be skipped (lines 381-383 in seedSchoolSettings)
            $branchDataSeeder = new BranchDataSeederService();
            foreach ($branches as $branch) {
                $branchDataSeeder->seedBranchData($school->id, $branch->id);
            }

            // Seed school settings for ALL branches AFTER sessions exist
            // IMPORTANT: Each branch needs its own set of settings for proper multi-branch operation
            foreach ($branches as $branch) {
                $this->seedSchoolSettings($school, $branch);
            }

            \Log::info('Multi-branch school creation', [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'total_branches' => $numberOfBranches,
                'branch_ids' => $branches->pluck('id')->toArray()
            ]);
        }
        return $school;
    }

    protected function storeSubscription($request, $school, $payment_method)
    {
        $features             = [];
        $featuresName         = [];
        $package              = Package::where('id', $request['package_id'])->first();
        $source = 'admin';
        $trx_id = Str::uuid();

        foreach (@$package->packageChilds ?? [] as $value) {
            $features[]       = @$value->feature->key;
            $featuresName[]   = @$value->feature->title;
        }

        if ($package->duration == PricingDuration::DAYS) {
            $expiryDate = date("Y-m-d H:i:s", strtotime("+ " . $package->duration_number . " day"));
        } elseif ($package->duration == PricingDuration::MONTHLY) {
            $expiryDate = date("Y-m-d H:i:s", strtotime("+ " . $package->duration_number . " month"));
        } elseif ($package->duration == PricingDuration::YEARLY) {
            $expiryDate = date("Y-m-d H:i:s", strtotime("+ " . $package->duration_number . " year"));
        }

        // Calculate pricing based on number of branches
        $numberOfBranches = $request->number_of_branches ?? 1;
        $basePrice = (int) @$package->price;
        $totalPrice = $basePrice * $numberOfBranches;

        $old_school = false;
        if (Subscription::where('school_id', $school->id)->first()) {
            $old_school = true;
        }

        $subscription                     = new Subscription();
        $subscription->package_id         = @$package->id;
        $subscription->price              = $totalPrice;  // Total price = base price × number of branches
        $subscription->student_limit      = @$package->student_limit;
        $subscription->staff_limit        = @$package->staff_limit;
        $subscription->expiry_date        = $expiryDate ? date('Y-m-d', strtotime($expiryDate)) : null;
        $subscription->features_name      = $featuresName;
        $subscription->features           = $features;
        $subscription->school_id          = @$school->id;
        $subscription->status             = 0;
        // $subscription->payment_status     = $source == 'website' ? 1 : 0;

        $subscription->trx_id             = @$trx_id;
        $subscription->method             = $payment_method;

        // Set branch-aware pricing fields
        $subscription->branch_count       = $numberOfBranches;
        $subscription->total_price        = $totalPrice;

        $subscription->save();

        \Log::info('Subscription created with multi-branch pricing', [
            'subscription_id' => $subscription->id,
            'school_id' => $school->id,
            'package_id' => $package->id,
            'base_price' => $basePrice,
            'number_of_branches' => $numberOfBranches,
            'total_price' => $totalPrice
        ]);

        return $subscription;
    }


    public function show($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get school with package and subscriptions
     *
     * @param int $id School ID
     * @return School|null
     */
    public function getSchoolWithPackage(int $id): ?School
    {
        return $this->model->with(['package', 'subscriptions' => function($query) {
            $query->latest()->limit(1);
        }])->find($id);
    }

    public function update($request, $id)
    {
        try {

            $row                 = $this->model->findOrfail($id);
            $row->name           = $request->name;
            $row->status         = $request->status;
            $row->save();

            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->find($id);

            if (!$row) {
                return $this->responseWithError(___('alert.school_not_found'), []);
            }

            // Check for related data before deletion
            $subscriptionCount = $row->subscriptions()->count();

            // Build error message if there are related records
            $relatedData = [];

            if ($subscriptionCount > 0) {
                $relatedData[] = "{$subscriptionCount} subscription(s)";
            }

            if (!empty($relatedData)) {
                $errorMessage = "Cannot delete this school. It has " . implode(' and ', $relatedData) . " associated with it. Please remove or reassign these records first.";
                DB::rollback();
                return $this->responseWithError($errorMessage, []);
            }

            $row->delete();

            $tenant = Tenant::where('id', $row->sub_domain_key)->first();
            $tenant->delete();

            $dbConnection = config('database.default'); // Get the default database connection name from config
            $dbConfig = config("database.connections.$dbConnection");

            $dbh = new PDO(
                "mysql:host={$dbConfig['host']};port={$dbConfig['port']}",
                $dbConfig['username'],
                $dbConfig['password']
            );

            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dbName = $tenant->tenancy_db_name;

            $sql = "DROP DATABASE IF EXISTS $dbName"; // Add IF EXISTS to avoid errors if the database doesn't exist
            $result = $dbh->exec($sql);

            DB::beginTransaction();
            DB::commit();
            if ($result !== false) {
                return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
            } else {
                return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    protected function createSchoolAdminUser($school, $request, $branch = null)
    {
        try {
            User::create([
                'name' => $request['admin_name'],
                'email' => $request['admin_email'],
                'password' => Hash::make($request['admin_password']),
                'username' => $request['admin_email'],
                'school_id' => $school->id,
                'role_id' => 1, // Super Admin role
                'branch_id' => $branch ? $branch->id : 1,
                'email_verified_at' => now(),
                'status' => 1,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Failed to create school admin user: ' . $th->getMessage());
        }
    }

    /**
     * Seed initial settings for newly created school from reference school
     *
     * @param School $school Newly created school
     * @param \Modules\MultiBranch\Entities\Branch $branch Main branch for the school
     * @return void
     */
    protected function seedSchoolSettings(School $school, $branch): void
    {
        try {
            // 1. Find reference school with complete settings (30+ settings)
            $referenceSchool = DB::table('settings')
                ->select('school_id', DB::raw('COUNT(*) as settings_count'))
                ->groupBy('school_id')
                ->having('settings_count', '>=', 30)
                ->orderBy('school_id', 'asc')
                ->first();

            if (!$referenceSchool) {
                \Log::warning('No reference school found for settings seeding', [
                    'school_id' => $school->id
                ]);
                return;
            }

            $referenceSchoolId = $referenceSchool->school_id;

            // 2. Get all settings from reference school
            $referenceSettings = DB::table('settings')
                ->where('school_id', $referenceSchoolId)
                ->get(['name', 'value'])
                ->keyBy('name')
                ->toArray();

            if (empty($referenceSettings)) {
                \Log::warning('Reference school has no settings', [
                    'reference_school_id' => $referenceSchoolId,
                    'target_school_id' => $school->id
                ]);
                return;
            }

            // 3. Get target school's active session for this specific branch
            $activeSession = DB::table('sessions')
                ->where('school_id', $school->id)
                ->where('branch_id', $branch->id)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->first();

            // 4. Prepare settings to insert
            $settingsToInsert = [];
            $now = now();

            foreach ($referenceSettings as $settingName => $referenceSetting) {
                $value = $this->getSettingValue(
                    $settingName,
                    $referenceSetting->value,
                    $school,
                    $activeSession
                );

                // Skip if value is null (e.g., session with no active session)
                if ($value === null && $settingName === 'session') {
                    continue;
                }

                $settingsToInsert[] = [
                    'school_id' => $school->id,
                    'branch_id' => $branch->id,
                    'name' => $settingName,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 5. Insert all settings
            if (!empty($settingsToInsert)) {
                DB::table('settings')->insert($settingsToInsert);

                \Log::info('School settings seeded successfully', [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'branch_id' => $branch->id,
                    'reference_school_id' => $referenceSchoolId,
                    'settings_count' => count($settingsToInsert)
                ]);
            }

        } catch (\Throwable $th) {
            // Log error but don't break school creation
            \Log::error('Failed to seed school settings', [
                'school_id' => $school->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Don't throw - settings seeding failure shouldn't break school creation
            // Settings can be populated manually later if needed
        }
    }

    /**
     * Get appropriate value for a setting based on its name and context
     * Sensitive data (passwords, API keys) are replaced with placeholders
     *
     * @param string $settingName
     * @param mixed $referenceValue
     * @param School $school
     * @param object|null $activeSession
     * @return mixed
     */
    protected function getSettingValue(
        string $settingName,
        $referenceValue,
        School $school,
        $activeSession
    ) {
        // List of sensitive settings that should use placeholders
        $sensitiveSettings = [
            'MAIL_PASSWORD',
            'mail_password',
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
            'aws_access_key_id',
            'aws_secret_access_key',
            'STRIPE_SECRET',
            'PAYPAL_SECRET',
            'stripe_secret_key',
            'paypal_secret',
            'firebase_private_key',
            'twilio_auth_token',
        ];

        // Check if this is a sensitive setting
        if (in_array($settingName, $sensitiveSettings) ||
            str_contains(strtolower($settingName), 'password') ||
            str_contains(strtolower($settingName), 'secret') ||
            str_contains(strtolower($settingName), 'api_key') ||
            str_contains(strtolower($settingName), 'private_key') ||
            str_contains(strtolower($settingName), 'token')) {

            return 'PLACEHOLDER_' . strtoupper($settingName) . '_CHANGE_THIS';
        }

        // Special handling for specific settings
        switch ($settingName) {
            case 'session':
                // Use target school's active session if available
                if ($activeSession) {
                    return (string) $activeSession->id;
                }
                // Return null if no active session - will be skipped
                return null;

            case 'application_name':
                return $school->name . ' - Management System';

            case 'footer_text':
                return '© ' . now()->year . ' ' . $school->name . '. All rights reserved.';

            case 'use_enhanced_fee_system':
                // Always enable enhanced fee system for new schools (uses student services/subscriptions)
                return '1';

            // For all other non-sensitive settings, copy from reference school
            default:
                return $referenceValue;
        }
    }

    /**
     * Create multiple branches for newly created school
     *
     * @param School $school
     * @param int $numberOfBranches
     * @return \Illuminate\Support\Collection
     */
    protected function createMultipleBranches($school, int $numberOfBranches = 1)
    {
        $branches = collect();

        try {
            // Check if MultiBranch module is active
            if (!class_exists('\Modules\MultiBranch\Entities\Branch')) {
                \Log::warning('MultiBranch module not active, branches not created');
                return $branches;
            }

            $branchClass = '\Modules\MultiBranch\Entities\Branch';
            $model = new $branchClass;
            $fillable = $model->getFillable();

            // Create the specified number of branches
            for ($i = 1; $i <= $numberOfBranches; $i++) {
                $branchData = [
                    'name' => $i === 1 ? 'Main Branch' : "{$school->name} - Branch {$i}",
                    'address' => $school->address,
                    'phone' => $school->phone,
                    'email' => $school->email,
                    'status' => 1, // Active
                ];

                // Add optional fields if they exist in the schema
                if (in_array('school_id', $fillable)) {
                    $branchData['school_id'] = $school->id;
                }
                if (in_array('code', $fillable)) {
                    $branchData['code'] = $i === 1 ? 'MAIN' : 'BRANCH' . $i;
                }
                if (in_array('is_default', $fillable)) {
                    $branchData['is_default'] = ($i === 1); // First branch is default
                }
                if (in_array('country_id', $fillable)) {
                    $branchData['country_id'] = 1; // Default country
                }

                $branch = $branchClass::create($branchData);
                $branches->push($branch);

                \Log::info('Created branch for school', [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'branch_number' => $i,
                    'is_main' => ($i === 1)
                ]);
            }

            \Log::info('Successfully created all branches', [
                'school_id' => $school->id,
                'total_branches' => $numberOfBranches,
                'branch_ids' => $branches->pluck('id')->toArray()
            ]);

        } catch (\Throwable $th) {
            \Log::error('Failed to create branches', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'school_id' => $school->id,
                'requested_branches' => $numberOfBranches,
                'created_branches' => $branches->count()
            ]);

            // Re-throw to trigger transaction rollback
            throw $th;
        }

        return $branches;
    }

    /**
     * Generate a unique subdomain key from school name
     *
     * @param string $schoolName
     * @return string
     */
    protected function generateSubDomainKey(string $schoolName): string
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $baseKey = Str::slug($schoolName);

        // Ensure uniqueness by appending number if needed
        $subDomainKey = $baseKey;
        $counter = 1;

        while (School::where('sub_domain_key', $subDomainKey)->exists()) {
            $subDomainKey = $baseKey . '-' . $counter;
            $counter++;
        }

        return $subDomainKey;
    }
}
