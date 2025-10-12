# Dynamic Report Form - Quick Start Guide

## 5-Minute Setup

### Step 1: Build Frontend Assets

```bash
npm run build
# or for development
npm run dev
```

### Step 2: Create Route

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
});

// routes/api.php
Route::middleware(['auth:sanctum'])->prefix('reports')->group(function () {
    Route::get('/', [ReportApiController::class, 'index'])
        ->name('api.reports.index');
    Route::get('/{report}/parameters', [ReportApiController::class, 'parameters']);
    Route::get('/parameters/{parameter}/dependent-values', [ReportApiController::class, 'dependentValues']);
    Route::post('/{report}/execute', [ReportApiController::class, 'execute']);
    Route::post('/{report}/export/{format}', [ReportApiController::class, 'export']);
});
```

### Step 3: Create Controllers

```php
// app/Http/Controllers/ReportController.php
<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
}
```

```php
// app/Http/Controllers/Api/ReportApiController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportParameter;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    /**
     * Get all reports grouped by category
     */
    public function index()
    {
        $categories = ReportCategory::with(['reports' => function ($query) {
            $query->where('is_active', true)
                  ->orderBy('display_order');
        }])->orderBy('display_order')->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Get parameters for a specific report
     */
    public function parameters(Report $report)
    {
        $parameters = $report->parameters()
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'report' => $report->only(['id', 'name', 'description']),
            'parameters' => $parameters
        ]);
    }

    /**
     * Get dependent values for cascading dropdowns
     */
    public function dependentValues(Request $request, ReportParameter $parameter)
    {
        $parentValue = $request->input('parent_value');

        // Call your stored procedure or query to get dependent values
        $values = $this->getDependentValues($parameter, $parentValue);

        return response()->json([
            'success' => true,
            'values' => $values
        ]);
    }

    /**
     * Execute report with parameters
     */
    public function execute(Request $request, Report $report)
    {
        // Validate parameters
        $validated = $this->validateParameters($request, $report);

        // Execute report (call stored procedure)
        $results = $this->executeReport($report, $validated);

        return response()->json([
            'success' => true,
            'data' => $results['data'],
            'columns' => $results['columns']
        ]);
    }

    /**
     * Export report to Excel/PDF/CSV
     */
    public function export(Request $request, Report $report, string $format)
    {
        $validated = $this->validateParameters($request, $report);
        $results = $this->executeReport($report, $validated);

        switch ($format) {
            case 'excel':
                return Excel::download(
                    new ReportExport($results),
                    "{$report->name}.xlsx"
                );

            case 'pdf':
                return PDF::loadView('reports.pdf', $results)
                    ->download("{$report->name}.pdf");

            case 'csv':
                return Excel::download(
                    new ReportExport($results),
                    "{$report->name}.csv",
                    \Maatwebsite\Excel\Excel::CSV
                );

            default:
                abort(400, 'Invalid export format');
        }
    }

    /**
     * Helper: Get dependent values
     */
    private function getDependentValues(ReportParameter $parameter, $parentValue)
    {
        // Example: Load sections based on class_id
        if ($parameter->name === 'p_section_id') {
            return \DB::table('class_sections')
                ->where('class_id', $parentValue)
                ->select('id as value', 'name as label')
                ->get();
        }

        // Add more dependency logic as needed
        return [];
    }

    /**
     * Helper: Validate parameters
     */
    private function validateParameters(Request $request, Report $report)
    {
        $rules = [];
        $parameters = $report->parameters;

        foreach ($parameters as $param) {
            $rules[$param->name] = [];

            if ($param->is_required) {
                $rules[$param->name][] = 'required';
            } else {
                $rules[$param->name][] = 'nullable';
            }

            // Add type-specific rules
            switch ($param->type) {
                case 'date':
                    $rules[$param->name][] = 'date';
                    break;
                case 'number':
                    $rules[$param->name][] = 'numeric';
                    break;
                case 'email':
                    $rules[$param->name][] = 'email';
                    break;
            }
        }

        return $request->validate($rules);
    }

    /**
     * Helper: Execute report
     */
    private function executeReport(Report $report, array $parameters)
    {
        // Example: Call stored procedure
        $procedureName = $report->stored_procedure_name;

        // Build parameter string for procedure
        $paramString = collect($parameters)
            ->map(fn($value, $key) => ":$key")
            ->values()
            ->join(', ');

        // Execute procedure
        $results = \DB::select(
            "CALL {$procedureName}({$paramString})",
            $parameters
        );

        // Get column definitions
        $columns = $report->columns()->orderBy('display_order')->get();

        return [
            'data' => $results,
            'columns' => $columns
        ];
    }
}
```

### Step 4: Create Models

```php
// app/Models/Report.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'stored_procedure_name',
        'is_active',
        'display_order'
    ];

    public function category()
    {
        return $this->belongsTo(ReportCategory::class);
    }

    public function parameters()
    {
        return $this->hasMany(ReportParameter::class);
    }

    public function columns()
    {
        return $this->hasMany(ReportColumn::class);
    }
}
```

```php
// app/Models/ReportParameter.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportParameter extends Model
{
    protected $fillable = [
        'report_id',
        'name',
        'label',
        'type',
        'is_required',
        'default_value',
        'description',
        'placeholder',
        'values',
        'depends_on',
        'parent_id',
        'min_value',
        'max_value',
        'step',
        'rows',
        'display_order'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'values' => 'array',
        'min_value' => 'float',
        'max_value' => 'float',
        'step' => 'float',
        'rows' => 'integer'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function parent()
    {
        return $this->belongsTo(ReportParameter::class, 'parent_id');
    }
}
```

### Step 5: Database Migrations

```php
// database/migrations/xxxx_create_reports_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Report Categories
        Schema::create('report_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('report_categories');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('stored_procedure_name');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Report Parameters
        Schema::create('report_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // p_start_date, p_class_id
            $table->string('label'); // "Start Date", "Class"
            $table->string('type'); // date, text, number, select, etc.
            $table->boolean('is_required')->default(false);
            $table->string('default_value')->nullable();
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('values')->nullable(); // For select/multiselect
            $table->string('depends_on')->nullable(); // Parent parameter name
            $table->foreignId('parent_id')->nullable(); // Parent parameter ID
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->decimal('step', 10, 2)->nullable();
            $table->integer('rows')->nullable(); // For textarea
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['report_id', 'display_order']);
        });

        // Report Columns (for display)
        Schema::create('report_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Column name from query result
            $table->string('label'); // Display label
            $table->string('type')->default('text'); // text, number, date, currency
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_columns');
        Schema::dropIfExists('report_parameters');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_categories');
    }
};
```

### Step 6: Seed Sample Data

```php
// database/seeders/ReportSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportCategory;
use App\Models\Report;
use App\Models\ReportParameter;
use App\Models\ReportColumn;

class ReportSeeder extends Seeder
{
    public function run()
    {
        // Create category
        $financialCategory = ReportCategory::create([
            'name' => 'Financial Reports',
            'description' => 'Fee and payment related reports',
            'display_order' => 1
        ]);

        // Create report
        $unpaidReport = Report::create([
            'category_id' => $financialCategory->id,
            'name' => 'Unpaid Students Report',
            'description' => 'Lists students with unpaid fees for a specific period',
            'stored_procedure_name' => 'sp_unpaid_students_report',
            'display_order' => 1
        ]);

        // Create parameters
        ReportParameter::create([
            'report_id' => $unpaidReport->id,
            'name' => 'p_start_date',
            'label' => 'Start Date',
            'type' => 'date',
            'is_required' => true,
            'default_value' => now()->startOfMonth()->format('Y-m-d'),
            'placeholder' => 'Select start date',
            'display_order' => 1
        ]);

        ReportParameter::create([
            'report_id' => $unpaidReport->id,
            'name' => 'p_end_date',
            'label' => 'End Date',
            'type' => 'date',
            'is_required' => true,
            'default_value' => now()->format('Y-m-d'),
            'placeholder' => 'Select end date',
            'display_order' => 2
        ]);

        $classParam = ReportParameter::create([
            'report_id' => $unpaidReport->id,
            'name' => 'p_class_id',
            'label' => 'Class',
            'type' => 'select',
            'is_required' => false,
            'placeholder' => 'Select class',
            'values' => [
                ['value' => 1, 'label' => 'Grade 1'],
                ['value' => 2, 'label' => 'Grade 2'],
                ['value' => 3, 'label' => 'Grade 3']
            ],
            'display_order' => 3
        ]);

        ReportParameter::create([
            'report_id' => $unpaidReport->id,
            'name' => 'p_section_id',
            'label' => 'Section',
            'type' => 'select',
            'is_required' => false,
            'placeholder' => 'Select section',
            'depends_on' => 'p_class_id',
            'parent_id' => $classParam->id,
            'display_order' => 4
        ]);

        // Create columns
        ReportColumn::create([
            'report_id' => $unpaidReport->id,
            'name' => 'student_name',
            'label' => 'Student Name',
            'type' => 'text',
            'display_order' => 1
        ]);

        ReportColumn::create([
            'report_id' => $unpaidReport->id,
            'name' => 'enrollment_number',
            'label' => 'Enrollment Number',
            'type' => 'text',
            'display_order' => 2
        ]);

        ReportColumn::create([
            'report_id' => $unpaidReport->id,
            'name' => 'amount_due',
            'label' => 'Amount Due',
            'type' => 'currency',
            'display_order' => 3
        ]);
    }
}
```

### Step 7: Access the Report

```
Navigate to: http://your-app.test/reports
```

## Complete Example Flow

1. **User opens reports page**
   - Categories load automatically
   - Report selector is populated

2. **User selects a report**
   - Parameters are fetched from API
   - Form is dynamically generated

3. **User fills parameters**
   - Class dropdown is populated
   - User selects "Grade 2"
   - Section dropdown automatically loads sections for Grade 2

4. **User clicks Generate Report**
   - Form is validated
   - API call executes report
   - Results display in table

5. **User exports report**
   - Clicks "Export Excel"
   - File downloads automatically

## Common Customizations

### Add Custom Parameter Type

```javascript
// In DynamicReportForm.js
renderParameterInput(param, baseAttrs) {
    switch (param.type) {
        // ... existing cases

        case 'color':
            return this.renderColorPicker(param, baseAttrs);

        default:
            return this.renderTextInput(param, baseAttrs);
    }
}

renderColorPicker(param, baseAttrs) {
    return `
        <input
            type="color"
            class="form-control form-control-color"
            value="${param.default_value || '#000000'}"
            ${baseAttrs}
        />
    `;
}
```

### Customize Export Filename

```php
public function export(Request $request, Report $report, string $format)
{
    $timestamp = now()->format('Y-m-d_His');
    $filename = "{$report->name}_{$timestamp}.{$format}";

    // ... export logic
}
```

### Add Loading Animation

```css
/* In your custom CSS */
.loading-overlay {
    background: rgba(255, 255, 255, 0.95);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
```

## Next Steps

1. **Add more reports** by creating database records
2. **Customize styling** in the Blade template
3. **Add export templates** for PDF generation
4. **Implement caching** for report results
5. **Add scheduling** for automated reports

## Support

For detailed documentation, see:
- `/docs/DYNAMIC_REPORT_FORM_DOCUMENTATION.md`

For issues:
- Check browser console for errors
- Verify API responses match expected format
- Check Laravel logs for backend errors

---

**Happy Reporting!** 🎉
