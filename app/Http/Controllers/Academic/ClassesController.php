<?php

namespace App\Http\Controllers\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Repositories\LanguageRepository;
use App\Interfaces\Academic\ClassesInterface;
use App\Http\Requests\Academic\Classes\ClassesStoreRequest;
use App\Http\Requests\Academic\Classes\ClassesUpdateRequest;
use App\Models\Academic\Classes;
use Illuminate\Support\Facades\Log;

class ClassesController extends Controller
{
    private $classes;
    private $lang_repo;

    function __construct(ClassesInterface $classes, LanguageRepository $lang_repo)
    {

        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->classes       = $classes;
        $this->lang_repo       = $lang_repo;
    }

    public function index()
    {
        $data['class'] = $this->classes->getAll();
        $data['title'] = ___('academic.class');
        
        // Check for classes without academic levels
        $classesWithoutLevels = $data['class']->filter(function ($class) {
            return !$class->hasAcademicLevel();
        });
        
        if ($classesWithoutLevels->count() > 0) {
            $message = "Warning: {$classesWithoutLevels->count()} classes do not have academic levels assigned. " .
                      "This may cause issues with fee assignment during student registration. " .
                      "Please assign academic levels to ensure proper fee calculation.";
            
            session()->flash('warning', $message);
            
            // Optional: Provide a quick fix link
            session()->flash('action_link', [
                'url' => url('artisan-command?cmd=classes:assign-academic-levels'),
                'text' => 'Auto-assign Academic Levels'
            ]);
        }
        
        return view('backend.academic.class.index', compact('data'));
    }

    public function create()
    {
        $data['title']       = ___('academic.create_class');
        return view('backend.academic.class.create', compact('data'));
    }

    public function store(ClassesStoreRequest $request)
    {
        $result = $this->classes->store($request);
        if($result['status']){
            return redirect()->route('classes.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['class']       = $this->classes->show($id);
        $data['title']       = ___('academic.edit_class');
        return view('backend.academic.class.edit', compact('data'));
    }

    public function translate($id)
    {
        $data['class']        = $this->classes->show($id);
        $data['translates']      = $this->classes->translates($id);
        $data['languages']      = $this->lang_repo->all();
        $data['title']       = ___('academic.edit_class');
        return view('backend.academic.class.translate', compact('data'));
    }

    public function translateUpdate(Request $request, $id){

        $result = $this->classes->translateUpdate($request, $id);
        if($result['status']){
            return redirect()->route('classes.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function update(ClassesUpdateRequest $request, $id)
    {
        $result = $this->classes->update($request, $id);
        if($result['status']){
            return redirect()->route('classes.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->classes->destroy($id);
        if($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;
    }

    /**
     * Show academic level management interface
     */
    public function academicLevelManagement()
    {
        $data['classes'] = Classes::orderBy('name')->get();
        $data['title'] = 'Academic Level Management';
        $data['statistics'] = Classes::getAcademicLevelCounts();
        
        return view('backend.academic.class.academic-level-management', compact('data'));
    }

    /**
     * Bulk assign academic levels
     */
    public function bulkAssignAcademicLevels(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.class_id' => 'required|exists:classes,id',
            'assignments.*.academic_level' => 'required|in:kg,primary,secondary,high_school'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($request->assignments as $assignment) {
            try {
                $class = Classes::find($assignment['class_id']);
                if ($class) {
                    $class->update(['academic_level' => $assignment['academic_level']]);
                    $successCount++;
                    
                    Log::info('Academic level assigned via bulk management', [
                        'class_id' => $class->id,
                        'class_name' => $class->name,
                        'academic_level' => $assignment['academic_level'],
                        'assigned_by' => auth()->id()
                    ]);
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to update class ID {$assignment['class_id']}: " . $e->getMessage();
                Log::error('Bulk academic level assignment failed', [
                    'class_id' => $assignment['class_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($successCount > 0) {
            $message = "Successfully assigned academic levels to {$successCount} classes.";
            if (!empty($errors)) {
                $message .= " However, " . count($errors) . " assignments failed.";
            }
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'No academic levels were assigned. Please check the errors.');
    }

    /**
     * Get academic level suggestion for a class
     */
    public function suggestAcademicLevel(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);
        
        $class = Classes::find($request->class_id);
        $suggestion = $class->suggestAcademicLevel();
        
        return response()->json([
            'success' => true,
            'suggestion' => $suggestion,
            'class_name' => $class->name
        ]);
    }
}
