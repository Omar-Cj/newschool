# Routines System Documentation

**Laravel School Management System - Academic Scheduling Feature**

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [System Overview](#system-overview)
3. [Architecture Deep Dive](#architecture-deep-dive)
4. [Core Components](#core-components)
5. [User Workflows](#user-workflows)
6. [API Integration](#api-integration)
7. [Business Rules & Validation](#business-rules--validation)
8. [Configuration & Setup](#configuration--setup)
9. [Troubleshooting](#troubleshooting)
10. [Developer Reference](#developer-reference)

---

## Executive Summary

The **Routines System** is a core academic scheduling feature integrated into the Laravel School Management System. It provides comprehensive timetable management for both regular classes and examinations, supporting multi-shift operations, mobile access, and role-based permissions.

### Key Benefits
- **Centralized Scheduling**: Unified system for class and exam timetables
- **Conflict Prevention**: Built-in validation to prevent scheduling conflicts
- **Multi-Platform Access**: Web interface + mobile API support
- **Role-Based Views**: Different access levels for admins, teachers, students, parents
- **Session Management**: Academic year-based organization with multi-tenant support

---

## System Overview

### What are Routines?

The Routines system manages academic schedules through two primary components:

1. **Class Routines** - Daily/weekly timetables for regular academic classes
2. **Exam Routines** - Specialized scheduling for examinations and assessments

### Why is it Important?

- **Academic Organization**: Provides structure for daily school operations
- **Resource Management**: Optimizes classroom and teacher utilization
- **Student Communication**: Ensures students know when and where classes occur
- **Parent Engagement**: Allows parents to track their children's schedules
- **Administrative Control**: Centralized management of all scheduling activities

### Integration Context

The Routines system is **not a separate module** but a core feature built into the main application, integrating seamlessly with:

- **Academic Sessions** - Yearly/semester organization
- **Class Management** - Classes and sections structure
- **Subject Assignment** - Teacher-subject-class relationships
- **Resource Management** - Classrooms and time schedules
- **User Management** - Staff, student, and parent access

---

## Architecture Deep Dive

### Database Schema

#### Core Tables Structure

**Class Routines Schema:**
```sql
-- Parent table: class_routines
- id (primary key)
- session_id (foreign key to sessions)
- classes_id (foreign key to classes) 
- section_id (foreign key to sections)
- shift_id (foreign key to shifts, nullable)
- day (tinyint: Saturday=1, Sunday=2, ..., Friday=7)
- timestamps

-- Child table: class_routine_childrens  
- id (primary key)
- class_routine_id (foreign key to class_routines)
- subject_id (foreign key to subjects)
- time_schedule_id (foreign key to time_schedules)
- class_room_id (foreign key to class_rooms)
- timestamps
```

**Exam Routines Schema:**
```sql
-- Parent table: exam_routines
- id (primary key)
- session_id (foreign key to sessions)
- classes_id (foreign key to classes)
- section_id (foreign key to sections) 
- type_id (foreign key to exam_types)
- date (string, nullable)
- timestamps

-- Child table: exam_routine_childrens
- id (primary key)
- exam_routine_id (foreign key to exam_routines)
- subject_id (foreign key to subjects)
- time_schedule_id (foreign key to time_schedules)
- class_room_id (foreign key to class_rooms)
- timestamps
```

### Parent-Child Relationship Benefits

The parent-child architecture provides:

1. **Flexible Scheduling**: One routine day can have multiple time periods
2. **Resource Optimization**: Different subjects can use different classrooms
3. **Teacher Assignment**: Each time slot can have different instructors
4. **Data Integrity**: Cascading deletes maintain referential integrity
5. **Query Efficiency**: Optimized for filtering and reporting

### Model Relationships

**ClassRoutine Model Relationships:**
```php
// Core relationships
belongsTo(Classes::class) // class
belongsTo(Section::class) // section  
belongsTo(Shift::class) // shift
hasMany(ClassRoutineChildren::class) // detailed schedule items

// ClassRoutineChildren relationships
belongsTo(ClassRoutine::class) // parent routine
belongsTo(Subject::class) // subject
belongsTo(TimeSchedule::class) // time slot
belongsTo(ClassRoom::class) // room assignment
belongsTo(Staff::class) // teacher (via subject assignment)
```

---

## Core Components

### 1. Class Routines - Daily Academic Scheduling

#### Purpose
Manages regular weekly class schedules, defining when each subject is taught, in which classroom, and at what time.

#### Key Features
- **Day-based Scheduling**: Covers Saturday through Friday (1-7)
- **Multi-shift Support**: Morning, evening, or custom shifts
- **Subject Integration**: Links with subject assignment system
- **Resource Allocation**: Assigns classrooms and time slots
- **Teacher Tracking**: Connects with staff assignments

#### Use Cases
- **Weekly Timetables**: Standard Mon-Fri or Sat-Thu academic schedules
- **Subject Rotation**: Different subjects on different days
- **Resource Planning**: Classroom and equipment allocation
- **Teacher Workload**: Distribute teaching assignments across days
- **Student Planning**: Help students prepare for daily classes

#### Data Flow
1. **Admin creates** class routine for specific class/section/day
2. **System validates** no conflicts exist for the combination
3. **Admin adds subjects** with time slots and classroom assignments
4. **Students/Teachers access** through web or mobile API
5. **Parents view** their children's schedules through parent portal

### 2. Exam Routines - Examination Scheduling

#### Purpose
Specialized scheduling system for examinations, tests, and assessment periods.

#### Key Features   
- **Date-specific Scheduling**: Exact dates rather than weekly patterns
- **Exam Type Integration**: Links with different examination categories
- **Session-based Organization**: Tied to academic year/semester
- **Resource Management**: Classroom and time allocation for exams
- **Multi-class Support**: Handle multiple classes taking same exam

#### Use Cases
- **Final Examinations**: End-of-semester comprehensive exams
- **Mid-term Assessments**: Regular testing periods
- **Special Examinations**: Make-up exams, entrance tests
- **Resource Planning**: Prevent double-booking of exam halls
- **Student Communication**: Notify students of exam times/locations

#### Data Flow
1. **Admin creates** exam routine for specific class/section/exam type
2. **System validates** date and resource availability
3. **Admin assigns subjects** with specific time slots and rooms
4. **Students access** exam schedules through API
5. **Teachers view** their examination supervision schedules

---

## User Workflows

### Administrator Workflows

#### Creating a Class Routine

**Prerequisites:**
- Academic session must be active
- Classes and sections must be defined
- Subjects must be assigned to teachers
- Time schedules must be configured
- Classrooms must be available

**Steps:**
1. **Navigate** to Academic → Class Routine
2. **Click** "Create New" 
3. **Select** Class, Section, and Day
4. **Choose** Shift (if multi-shift school)
5. **Add Subject Periods**:
   - Select subject from assigned subjects
   - Choose time schedule
   - Assign classroom
   - Repeat for all periods in the day
6. **Validate** - System checks for conflicts
7. **Save** - Creates routine and all child records

#### Creating an Exam Routine

**Prerequisites:**
- Exam types must be defined
- Academic session must be active
- Classes and sections must exist
- Examination dates must be planned

**Steps:**
1. **Navigate** to Examination → Exam Routine
2. **Click** "Create New"
3. **Select** Class, Section, Exam Type, and Date
4. **Add Exam Subjects**:
   - Choose subject
   - Set time schedule
   - Assign examination hall/classroom
   - Repeat for all exam subjects
5. **Review** for scheduling conflicts
6. **Save** - Creates complete exam schedule

### Teacher Workflows

#### Viewing Class Schedule (Web)

**Access Path:** Academic → Class Routine → View/Filter
- **Filter** by assigned classes/sections
- **View** daily/weekly schedule
- **Check** classroom and time allocations
- **Verify** subject assignments

#### Mobile API Access

**Endpoints Used:**
- `GET /api/teacher/routine/class` - Get class routines
- `GET /api/teacher/routine/exam` - Get exam routines

**Parameters:**
- `date` - Specific date (defaults to today)
- `class_id` - Filter by specific class (optional)
- `section_id` - Filter by specific section (optional)
- `exam_type` - Required for exam routines

### Student Workflows

#### Mobile App Access

**API Endpoint:** `GET /api/student/class-routines`

**Parameters:**
- `date` - Date to get schedule for (required)

**Response Includes:**
- Subject name and details
- Time schedule (start/end times)
- Classroom location
- Day of week information

**Usage Pattern:**
1. **Student logs in** to mobile app
2. **App determines** student's class and section automatically
3. **Student selects** date to view schedule
4. **App displays** subjects, times, and locations for that day

#### Parent Portal Access

**Access Path:** Parent Panel → Student Schedule
- **View** child's daily/weekly routines
- **Check** upcoming classes and exams
- **Verify** classroom locations
- **Track** schedule changes

---

## API Integration

### Authentication & Authorization

All API endpoints require:
- **Bearer token authentication**
- **Role-based permissions** (student, teacher, parent)
- **Session context** - automatically filtered by academic session

### Student API Endpoints

#### Get Class Routines
```http
GET /api/student/class-routines?date=2024-03-15
Authorization: Bearer {token}
```

**Response Format:**
```json
{
    "success": true,
    "message": "Success",
    "data": [
        {
            "subject": {
                "id": 1,
                "name": "Mathematics",
                "code": "MATH101"
            },
            "timeSchedule": {
                "start_time": "08:00:00",
                "end_time": "09:00:00"
            },
            "classRoom": {
                "name": "Room A-101",
                "capacity": 30
            }
        }
    ]
}
```

### Teacher API Endpoints

#### Get Class Routines
```http  
GET /api/teacher/routine/class?date=2024-03-15&class_id=1
Authorization: Bearer {token}
```

#### Get Exam Routines
```http
GET /api/teacher/routine/exam?exam_type=1
Authorization: Bearer {token}
```

**Query Parameters:**
- `date` - Filter by specific date
- `class_id` - Filter by class (optional)
- `section_id` - Filter by section (optional)
- `exam_type` - Required for exam routines

### Error Handling

**Common Error Responses:**
```json
{
    "success": false,
    "message": "Student not found"
}

{
    "success": false,
    "message": "No class routine found"
}

{
    "success": false,  
    "message": "You must select an exam type"
}
```

### Rate Limiting & Caching

- **API calls** are subject to standard Laravel rate limiting
- **Responses** are cached based on date and user context
- **Cache invalidation** occurs when routines are updated

---

## Business Rules & Validation

### Uniqueness Constraints

#### Class Routines
- **Cannot duplicate** same class + section + day + shift combination
- **System validates** before creation and updates
- **Error message**: "Already created class routine" for duplicates

#### Exam Routines  
- **Cannot duplicate** same class + section + exam type + date combination
- **Prevents** scheduling conflicts for examination periods

### Resource Validation

#### Classroom Conflicts
- **Validates** classroom availability for selected time slots
- **Prevents** double-booking of rooms
- **Checks** across both class and exam routines

#### Teacher Assignments
- **Verifies** teacher is assigned to the subject
- **Checks** subject assignment for the class/section
- **Validates** teacher availability (implicit through subject assignments)

### Session Context

- **All routines** are scoped to current academic session
- **Automatic filtering** by session prevents cross-year conflicts  
- **Session setting** controlled via `setting('session')` helper

### Active Status Filtering

- **Only active entities** are shown in schedules:
  - Active time schedules
  - Active subjects
  - Active classrooms
  - Active shifts
- **Inactive items** are excluded from API responses

### Permission-Based Access

#### Required Permissions
- **class_routine_read** - View class routines
- **class_routine_create** - Create new class routines
- **class_routine_update** - Modify existing routines  
- **class_routine_delete** - Remove routines
- **exam_routine_read** - View exam routines
- **exam_routine_create** - Create exam schedules
- **exam_routine_update** - Modify exam schedules
- **exam_routine_delete** - Remove exam schedules

#### Feature Flag Protection
- **All routine endpoints** protected by `FeatureCheck:routine` middleware
- **Can be disabled** in subscription or feature management
- **Graceful degradation** when feature is disabled

---

## Configuration & Setup

### Prerequisites

Before using the Routines system, ensure these components are configured:

#### 1. Academic Foundation
```bash
# Ensure these are set up first:
- Academic Sessions (current year/semester)
- Classes and Sections structure
- Subject definitions
- Teacher/Staff records
```

#### 2. Resource Management
```bash
# Required for scheduling:
- Time Schedules (class periods, exam slots)
- Classrooms/Locations
- Shifts (if multi-shift school)
- Exam Types (for examination scheduling)
```

#### 3. Subject Assignments
```bash
# Critical for teacher-subject linking:
- Subject assignments linking teachers to subjects and classes
- Staff assignments with proper permissions
```

### Feature Configuration

#### Enable Routines Feature
```php
// Feature flag in subscription/feature management
'routine' => true,

// Or in middleware configuration
'FeatureCheck:routine' // Applied to all routine routes
```

#### Permission Setup
```php
// Required permissions in database
permissions: [
    'class_routine_read',
    'class_routine_create', 
    'class_routine_update',
    'class_routine_delete',
    'exam_routine_read',
    'exam_routine_create',
    'exam_routine_update', 
    'exam_routine_delete'
]
```

### Database Migrations

**Run routine-related migrations:**
```bash
# Create routine tables
php artisan migrate --path=database/migrations/tenant
```

**Key migration files:**
- `2023_03_22_062321_create_class_routines_table.php`
- `2023_03_24_053514_create_class_routine_childrens_table.php`  
- `2023_04_07_045518_create_exam_routines_table.php`
- `2023_04_07_045719_create_exam_routine_childrens_table.php`

### Environment Configuration

**Multi-tenant Setup:**
```env
APP_SAAS=true
CACHE_DRIVER=redis # Recommended for routine caching
```

**Single School Setup:**
```env
APP_SAAS=false
CACHE_DRIVER=array # Sufficient for single tenant
```

---

## Troubleshooting

### Common Issues & Solutions

#### 1. "Already created class routine" Error

**Problem:** Attempting to create duplicate routine for same class/section/day/shift

**Solution:**
- Check existing routines for the combination
- Use Edit instead of Create for updates
- Verify shift selection (null vs specific shift)

**Debug Query:**
```php
ClassRoutine::where('session_id', setting('session'))
    ->where('classes_id', $classId)
    ->where('section_id', $sectionId)
    ->where('day', $day)
    ->where('shift_id', $shiftId)
    ->get();
```

#### 2. "Student not found" in API

**Problem:** Student API returns error when accessing routines

**Solution:**
- Verify student is properly enrolled
- Check student's class and section assignment  
- Ensure academic session is active
- Validate API token and permissions

**Debug Steps:**
```php
// Check student enrollment
$student = sessionClassStudent();
if (!$student) {
    // Student not enrolled or session issue
}
```

#### 3. Empty Routine Responses

**Problem:** API returns empty data even when routines exist

**Solution:**
- Verify active status of related entities:
  - Time schedules must be active
  - Subjects must be active
  - Classrooms must be active
- Check date format and day number conversion
- Validate session context

#### 4. Teacher Routine Access Issues

**Problem:** Teachers can't see their routines

**Solution:**
- Verify teacher has subject assignments
- Check subject assignments link to correct classes/sections
- Ensure teacher's staff record is properly configured
- Validate API parameters (exam_type for exam routines)

#### 5. Permission Denied Errors

**Problem:** Users get permission errors accessing routines

**Solution:**
- Verify required permissions are assigned
- Check feature flag is enabled (`FeatureCheck:routine`)
- Validate user role and subscription status
- Ensure middleware chain is correct

### Performance Optimization

#### Query Optimization
```php
// Use eager loading to prevent N+1 queries
ClassRoutineChildren::with([
    'subject', 
    'timeSchedule', 
    'classRoom', 
    'classRoutine'
])->get();
```

#### Caching Strategy
```php
// Cache routine data by date and user context
Cache::remember("routines_{$userId}_{$date}", 3600, function() {
    return $routineData;
});
```

### Debugging Tools

#### Enable Query Logging
```php
// In controller methods
DB::enableQueryLog();
// ... routine queries
dd(DB::getQueryLog());
```

#### API Response Debugging
```php
// Add to API controllers
Log::info('Routine API Request', [
    'user_id' => auth()->id(),
    'params' => request()->all(),
    'student_context' => sessionClassStudent()
]);
```

---

## Developer Reference

### Repository Pattern Implementation

#### ClassRoutineRepository

**Key Methods:**
```php
interface ClassRoutineInterface 
{
    public function all(); // Get all active routines for session
    public function getPaginateAll(); // Paginated list for admin
    public function store($request); // Create new routine with children
    public function update($request, $id); // Update routine and children  
    public function destroy($id); // Delete routine and cascade
    public function checkClassRoutine($request); // Validate uniqueness
    public function getSubjects($request); // Get assigned subjects
}
```

**Implementation Highlights:**
```php
public function store($request) 
{
    DB::beginTransaction();
    try {
        // Create parent routine
        $routine = new ClassRoutine();
        $routine->classes_id = $request->class;
        $routine->section_id = $request->section;
        $routine->session_id = setting('session');
        $routine->day = $request->day;
        $routine->save();

        // Create children for each subject/time slot
        foreach ($request->subjects ?? [] as $key => $subject) {
            ClassRoutineChildren::create([
                'class_routine_id' => $routine->id,
                'subject_id' => $subject,
                'time_schedule_id' => $request->time_schedules[$key],
                'class_room_id' => $request->class_rooms[$key]
            ]);
        }
        
        DB::commit();
        return $this->responseWithSuccess('Created successfully');
    } catch (\Throwable $th) {
        DB::rollback();
        return $this->responseWithError('Something went wrong');
    }
}
```

### Controller Architecture

#### ClassRoutineController Structure

```php
class ClassRoutineController extends Controller
{
    use ApiReturnFormatTrait;

    // Dependency injection for all required repositories
    public function __construct(
        ClassRoutineRepository $repo,
        SessionInterface $sessionRepo,
        ClassesInterface $classesRepo,
        // ... other dependencies
    ) {
        // Initialize repositories
    }

    public function index() // List view with pagination
    public function create() // Form for creating new routine  
    public function store(ClassRoutineStoreRequest $request) // Handle creation
    public function edit($id) // Edit form with existing data
    public function update(ClassRoutineUpdateRequest $request, $id) // Handle updates
    public function delete($id) // Soft delete with JSON response
    public function addClassRoutine(Request $request) // AJAX form components
    public function checkClassRoutine(Request $request) // Uniqueness validation
}
```

### API Controller Patterns

#### Student API Implementation

```php
public function index()
{
    // Validate student enrollment
    if (!sessionClassStudent()) {
        return $this->responseWithError('Student not found');
    }

    $student = sessionClassStudent();
    $dayNum = getDayNum(request('date'));

    // Query with relationships and filters
    $routines = ClassRoutineChildren::query()
        ->whereHas('classRoutine', function ($q) use ($student, $dayNum) {
            $q->where('classes_id', $student->classes_id)
              ->where('section_id', $student->section_id)
              ->where('session_id', $student->session_id)
              ->where('day', $dayNum);
        })
        ->with(['timeSchedule', 'subject', 'classRoom'])
        ->whereHas('timeSchedule', fn($q) => $q->class()->active())
        ->whereHas('subject', fn($q) => $q->active())
        ->whereHas('classRoom', fn($q) => $q->active())
        ->get();

    return $this->responseWithSuccess('Success', 
        StudentClassRoutineResource::collection($routines)
    );
}
```

### Request Validation

#### Form Request Classes

```php
class ClassRoutineStoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'class' => 'required',
            'section' => 'required', 
            'day' => 'required',
            'subjects' => 'array',
            'time_schedules' => 'array',
            'class_rooms' => 'array'
        ];
    }
}
```

### Resource Formatting

#### API Resource Classes

```php
class StudentClassRoutineResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subject' => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
                'code' => $this->subject->code
            ],
            'timeSchedule' => [
                'start_time' => $this->timeSchedule->start_time,
                'end_time' => $this->timeSchedule->end_time,
                'period' => $this->timeSchedule->period
            ],
            'classRoom' => [
                'name' => $this->classRoom->name,
                'capacity' => $this->classRoom->capacity
            ]
        ];
    }
}
```

### Helper Functions

#### Date Utilities

```php
// Convert date to day number (1-7)
function getDayNum($date) {
    return Carbon::parse($date)->dayOfWeek === 0 
        ? 7 // Sunday = 7
        : Carbon::parse($date)->dayOfWeek; // Monday = 1, etc.
}

// Get current session
function setting($key) {
    return Setting::where('key', $key)->value('value');
}

// Get student enrollment context
function sessionClassStudent() {
    return auth()->user()->student
        ->where('session_id', setting('session'))
        ->first();
}
```

### Database Query Patterns

#### Efficient Relationship Queries

```php
// Eager loading with constraints
$routines = ClassRoutine::with([
    'class:id,name',
    'section:id,name', 
    'classRoutineChildren' => function($query) {
        $query->with([
            'subject:id,name,code',
            'timeSchedule:id,start_time,end_time',
            'classRoom:id,name'
        ]);
    }
])
->where('session_id', setting('session'))
->active()
->get();
```

#### Performance Optimization

```php
// Index usage for common queries
Schema::table('class_routines', function (Blueprint $table) {
    $table->index(['session_id', 'classes_id', 'section_id', 'day']);
});

Schema::table('class_routine_childrens', function (Blueprint $table) {
    $table->index(['class_routine_id', 'time_schedule_id']);
});
```

### Testing Considerations

#### Unit Test Examples

```php
public function test_can_create_class_routine()
{
    $this->actingAs($this->admin)
         ->post('/class-routine/store', [
             'class' => 1,
             'section' => 1,
             'day' => 1,
             'subjects' => [1, 2],
             'time_schedules' => [1, 2],
             'class_rooms' => [1, 2]
         ])
         ->assertRedirect()
         ->assertSessionHas('success');
         
    $this->assertDatabaseHas('class_routines', [
        'classes_id' => 1,
        'section_id' => 1,
        'day' => 1
    ]);
}

public function test_prevents_duplicate_routines()
{
    // Create initial routine
    ClassRoutine::factory()->create([
        'classes_id' => 1,
        'section_id' => 1, 
        'day' => 1
    ]);
    
    // Attempt duplicate
    $response = $this->post('/class-routine/check-class-routine', [
        'class' => 1,
        'section' => 1,
        'day' => 1
    ]);
    
    $response->assertJson([
        'status' => false,
        'message' => 'Already created class routine'
    ]);
}
```

---

## Conclusion

The Routines System is a comprehensive academic scheduling solution that provides:

- **Flexible Architecture** supporting both regular classes and examinations
- **Multi-role Access** with appropriate permissions and API integration  
- **Conflict Prevention** through robust validation and business rules
- **Scalable Design** supporting multi-tenant and single-school deployments
- **Mobile Integration** with complete API coverage for external applications

For additional support or feature requests, refer to the main application documentation or contact the development team.

---

*Generated by Claude Code - School Management System Documentation*  
*Last Updated: September 7, 2025*