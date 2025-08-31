# Academic Core System Documentation
## School Management System

*Comprehensive guide to the academic management functionality in the Laravel-based school management system*

---

## Table of Contents

1. [System Architecture Overview](#system-architecture-overview)
2. [Core Entities Reference](#core-entities-reference)
3. [Database Schema & Relationships](#database-schema--relationships)
4. [Business Workflows](#business-workflows)
5. [Sample Data Scenarios](#sample-data-scenarios)
6. [API Integration Guide](#api-integration-guide)
7. [Implementation Best Practices](#implementation-best-practices)

---

## System Architecture Overview

The academic core system is built using a modular, hierarchical approach that manages the fundamental structure of educational institutions. The system supports both multi-tenant SaaS deployments and single-school installations.

### Academic Hierarchy

```
Academic Session (School Year)
├── Class Setup (Grade-Session Association)
    ├── Classes (Grade Levels)
    └── Class Setup Children (Section Assignments)
        └── Sections (Class Divisions)

Subject Management
├── Subjects (Individual Courses)
└── Subject Assignment
    ├── Class-Section Combinations
    ├── Teacher Assignments
    └── Subject-Teacher Mapping
```

### Key Features

- **Multi-tenant Architecture**: Supports multiple schools with data isolation
- **Multi-language Support**: Translation tables for international deployment
- **Status Management**: Active/Inactive states for all entities
- **Repository Pattern**: Clean separation of business logic
- **Cascade Protection**: Safe deletion with relationship preservation

---

## Core Entities Reference

### 1. Classes (Grade Levels)

**Purpose**: Represents academic grade levels or standards in the educational system.

**Database Table**: `classes`

**Key Fields**:
- `id` - Primary key
- `name` - Grade level name (e.g., "One", "Two", "Grade 9")
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\Classes`

**Relationships**:
- `hasOne(ClassSetup::class)` - Class setup association
- `hasOne(ClassTranslate::class)` - Translation support
- `hasMany(ClassTranslate::class)` - Multiple language translations

**Sample Data**:
```php
Classes::create(['name' => 'One']);
Classes::create(['name' => 'Two']);
Classes::create(['name' => 'Three']);
```

### 2. Sections (Class Divisions)

**Purpose**: Represents subdivisions within a class/grade to manage student groups.

**Database Table**: `sections`

**Key Fields**:
- `id` - Primary key
- `name` - Section identifier (e.g., "A", "B", "Rose", "Lily")
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\Section`

**Relationships**:
- `hasOne(ClassSectionTranslate::class)` - Translation support
- `hasMany(ClassSectionTranslate::class)` - Multiple language translations

**Sample Data**:
```php
Section::create(['name' => 'A']);
Section::create(['name' => 'B']);
```

### 3. Shifts (Time Periods)

**Purpose**: Manages different time schedules for school operations (morning, afternoon, evening shifts).

**Database Table**: `shifts`

**Key Fields**:
- `id` - Primary key
- `name` - Shift identifier (e.g., "1st", "2nd", "Morning", "Evening")
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\Shift`

**Relationships**:
- `hasOne(ShiftTranslate::class)` - Translation support
- `hasMany(ShiftTranslate::class)` - Multiple language translations

**Sample Data**:
```php
Shift::create(['name' => '1st']);
Shift::create(['name' => '2nd']);
Shift::create(['name' => '3rd']);
```

### 4. Subjects (Courses/Subjects)

**Purpose**: Manages individual academic subjects and courses offered by the institution.

**Database Table**: `subjects`

**Key Fields**:
- `id` - Primary key
- `name` - Subject name (e.g., "Mathematics", "English Literature")
- `code` - Subject code (e.g., "MATH101", "ENG102")
- `type` - Subject type (Theory = 1, Practical = 2)
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\Subject`

**Enums**:
```php
SubjectType::THEORY = 1;
SubjectType::PRACTICAL = 2;
```

**Relationships**:
- `hasMany(SubjectAssignChildren::class)` - Subject assignments

**Sample Data**:
```php
Subject::create(['name' => 'Mathematics', 'code' => '101', 'type' => 1]);
Subject::create(['name' => 'Physics', 'code' => '104', 'type' => 2]);
Subject::create(['name' => 'English', 'code' => '102', 'type' => 1]);
```

### 5. Class Setup (Session-Class Association)

**Purpose**: Links academic classes to specific academic sessions (school years).

**Database Table**: `class_setups`

**Key Fields**:
- `id` - Primary key
- `session_id` - Foreign key to sessions table
- `classes_id` - Foreign key to classes table
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\ClassSetup`

**Relationships**:
- `belongsTo(Session::class)` - Academic session
- `belongsTo(Classes::class)` - Associated class
- `hasMany(ClassSetupChildren::class)` - Section assignments

**Business Logic**: Enables classes to be offered in specific academic years with different section configurations.

### 6. Class Setup Children (Section Assignments)

**Purpose**: Associates sections with class setups, defining which sections are available for each class in a given session.

**Database Table**: `class_setup_childrens`

**Key Fields**:
- `id` - Primary key
- `class_setup_id` - Foreign key to class_setups table
- `section_id` - Foreign key to sections table
- `status` - Active/Inactive status
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Academic\ClassSetupChildren`

**Relationships**:
- `belongsTo(Classes::class)` - Associated class
- `belongsTo(Section::class)` - Associated section

### 7. Subject Assignment System

**Purpose**: Manages the assignment of subjects to specific class-section combinations with teacher allocations.

#### Subject Assigns

**Database Table**: `subject_assigns`

**Key Fields**:
- `id` - Primary key
- `session_id` - Foreign key to sessions table
- `classes_id` - Foreign key to classes table
- `section_id` - Foreign key to sections table
- `status` - Active/Inactive status

**Model**: `App\Models\Academic\SubjectAssign`

#### Subject Assign Children

**Database Table**: `subject_assign_childrens`

**Key Fields**:
- `id` - Primary key
- `subject_assign_id` - Foreign key to subject_assigns table
- `subject_id` - Foreign key to subjects table
- `staff_id` - Foreign key to staff table (teacher)
- `status` - Active/Inactive status

**Model**: `App\Models\Academic\SubjectAssignChildren`

---

## Database Schema & Relationships

### Entity Relationship Diagram

```
Sessions (Academic Years)
    ↓ (1:N)
Class Setups
    ↓ (1:N)                ← (N:1) Classes
Class Setup Children
    ↓ (N:1)
Sections

Subject Assigns
    ↓ (1:N)                ← (N:1) Sessions, Classes, Sections
Subject Assign Children
    ↓ (N:1)                ← (N:1) Subjects, Staff
```

### Foreign Key Relationships

1. **Class Setups**:
   - `session_id` → `sessions.id` (CASCADE DELETE)
   - `classes_id` → `classes.id` (CASCADE DELETE)

2. **Class Setup Children**:
   - `class_setup_id` → `class_setups.id` (CASCADE DELETE)
   - `section_id` → `sections.id` (CASCADE DELETE)

3. **Subject Assigns**:
   - `session_id` → `sessions.id` (CASCADE DELETE)
   - `classes_id` → `classes.id` (CASCADE DELETE)
   - `section_id` → `sections.id` (CASCADE DELETE)

4. **Subject Assign Children**:
   - `subject_assign_id` → `subject_assigns.id` (CASCADE DELETE)
   - `subject_id` → `subjects.id` (CASCADE DELETE)
   - `staff_id` → `staff.id` (CASCADE DELETE)

### Translation Tables

- `class_translates` - Multi-language support for classes
- `section_translates` - Multi-language support for sections  
- `shift_translates` - Multi-language support for shifts

---

## Business Workflows

### 1. Academic Year Setup Process

**Step 1: Create Academic Session**
```php
Session::create([
    'name' => '2024-2025',
    'start_date' => '2024-07-01',
    'end_date' => '2025-06-30'
]);
```

**Step 2: Create Class Setups**
```php
// Associate existing classes with the new session
ClassSetup::create([
    'session_id' => 1,
    'classes_id' => 1  // Grade "One"
]);
```

**Step 3: Add Section Assignments**
```php
// Add sections A and B to Grade One
ClassSetupChildren::create([
    'class_setup_id' => 1,
    'section_id' => 1  // Section "A"
]);

ClassSetupChildren::create([
    'class_setup_id' => 1,  
    'section_id' => 2  // Section "B"
]);
```

### 2. Subject Assignment Workflow

**Step 1: Create Subject Assignment Container**
```php
SubjectAssign::create([
    'session_id' => 1,
    'classes_id' => 1,  // Grade "One"
    'section_id' => 1   // Section "A"
]);
```

**Step 2: Assign Subjects and Teachers**
```php
SubjectAssignChildren::create([
    'subject_assign_id' => 1,
    'subject_id' => 1,  // Mathematics
    'staff_id' => 5     // Mr. Johnson
]);

SubjectAssignChildren::create([
    'subject_assign_id' => 1,
    'subject_id' => 2,  // English
    'staff_id' => 8     // Ms. Smith
]);
```

### 3. Query Examples

**Get all sections for a specific class in a session:**
```php
$classSetup = ClassSetup::where('session_id', 1)
    ->where('classes_id', 1)
    ->first();

$sections = $classSetup->classSetupChildrenAll()
    ->with('section')
    ->get();
```

**Get all subjects for a class-section:**
```php
$subjectAssign = SubjectAssign::where('session_id', 1)
    ->where('classes_id', 1)
    ->where('section_id', 1)
    ->first();

$subjects = SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
    ->with(['subject', 'staff'])
    ->get();
```

---

## Sample Data Scenarios

### Scenario 1: Greenwood Elementary School

**Institution Profile**:
- Type: Elementary School
- Grades: K-5 (Classes 1-6)
- Sections: 2 per grade (A, B)
- Shift: Single (Morning)
- Session: 2024-2025

**Setup Data**:

```php
// Classes
Classes::create(['name' => 'Kindergarten']);
Classes::create(['name' => 'One']);
Classes::create(['name' => 'Two']);
Classes::create(['name' => 'Three']);
Classes::create(['name' => 'Four']);
Classes::create(['name' => 'Five']);

// Sections
Section::create(['name' => 'A']);
Section::create(['name' => 'B']);

// Shift
Shift::create(['name' => 'Morning']);

// Subjects for Elementary
Subject::create(['name' => 'English Language Arts', 'code' => 'ELA101', 'type' => 1]);
Subject::create(['name' => 'Mathematics', 'code' => 'MATH101', 'type' => 1]);
Subject::create(['name' => 'Science', 'code' => 'SCI101', 'type' => 1]);
Subject::create(['name' => 'Social Studies', 'code' => 'SS101', 'type' => 1]);
Subject::create(['name' => 'Art', 'code' => 'ART101', 'type' => 2]);
Subject::create(['name' => 'Physical Education', 'code' => 'PE101', 'type' => 2]);
```

**Class Structure**:
- Each grade has 2 sections (A, B)
- Each section has ~25 students capacity
- Core subjects taught by homeroom teachers
- Special subjects (Art, PE) taught by specialist teachers

### Scenario 2: Metropolitan High School

**Institution Profile**:
- Type: High School
- Grades: 6-12
- Sections: 3 per grade (A, B, C)
- Shifts: 2 (Morning, Afternoon)
- Session: 2024-2025

**Setup Data**:

```php
// Classes
foreach(['Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve'] as $grade) {
    Classes::create(['name' => $grade]);
}

// Sections
foreach(['A', 'B', 'C'] as $section) {
    Section::create(['name' => $section]);
}

// Shifts
Shift::create(['name' => 'Morning']);
Shift::create(['name' => 'Afternoon']);

// High School Subjects
$subjects = [
    ['name' => 'Advanced Mathematics', 'code' => 'MATH201', 'type' => 1],
    ['name' => 'Physics', 'code' => 'PHY201', 'type' => 2],
    ['name' => 'Chemistry', 'code' => 'CHEM201', 'type' => 2],
    ['name' => 'Biology', 'code' => 'BIO201', 'type' => 2],
    ['name' => 'English Literature', 'code' => 'ENG201', 'type' => 1],
    ['name' => 'World History', 'code' => 'HIST201', 'type' => 1],
    ['name' => 'Computer Science', 'code' => 'CS201', 'type' => 2],
    ['name' => 'Foreign Language', 'code' => 'LANG201', 'type' => 1],
];

foreach($subjects as $subject) {
    Subject::create($subject);
}
```

**Complex Assignment Example**:
```php
// Grade 9, Section A, Morning Shift
$subjectAssign = SubjectAssign::create([
    'session_id' => 1,
    'classes_id' => 4, // Grade Nine
    'section_id' => 1  // Section A
]);

// Assign subjects with different teachers
$assignments = [
    ['subject_id' => 1, 'staff_id' => 10], // Math - Mr. Anderson
    ['subject_id' => 2, 'staff_id' => 15], // Physics - Dr. Wilson  
    ['subject_id' => 3, 'staff_id' => 18], // Chemistry - Ms. Davis
    ['subject_id' => 5, 'staff_id' => 22], // English - Mrs. Brown
];

foreach($assignments as $assignment) {
    SubjectAssignChildren::create(array_merge(
        ['subject_assign_id' => $subjectAssign->id],
        $assignment
    ));
}
```

### Scenario 3: Community College Prep Academy

**Institution Profile**:
- Type: Specialized High School
- Grades: 9-12
- Sections: Variable by subject demand
- Shifts: Flexible scheduling
- Focus: College preparation with advanced subjects

**Unique Features**:
- Subject-based sectioning (not traditional homeroom)
- Advanced Placement courses
- Dual enrollment options
- Flexible scheduling

**Sample Advanced Subject Setup**:
```php
$advancedSubjects = [
    ['name' => 'AP Calculus BC', 'code' => 'APCALC', 'type' => 1],
    ['name' => 'AP Physics C', 'code' => 'APPHYS', 'type' => 2],
    ['name' => 'AP Computer Science A', 'code' => 'APCS', 'type' => 2],
    ['name' => 'AP English Literature', 'code' => 'APENG', 'type' => 1],
    ['name' => 'Dual Enrollment Biology', 'code' => 'DEBIO', 'type' => 2],
    ['name' => 'SAT Prep Mathematics', 'code' => 'SATMATH', 'type' => 1],
];

foreach($advancedSubjects as $subject) {
    Subject::create($subject);
}
```

---

## API Integration Guide

### Core CRUD Operations

#### Classes Management

**Create a new class:**
```http
POST /api/classes
Content-Type: application/json

{
    "name": "Grade Six",
    "status": 1
}
```

**Get all active classes:**
```http
GET /api/classes?status=active
```

**Update class:**
```http
PUT /api/classes/{id}
Content-Type: application/json

{
    "name": "Grade Six Updated",
    "status": 1
}
```

#### Sections Management

**Create section:**
```http
POST /api/sections
Content-Type: application/json

{
    "name": "Rose",
    "status": 1
}
```

**Get sections with translations:**
```http
GET /api/sections?include=translations&locale=en
```

#### Subject Assignment

**Create subject assignment:**
```http
POST /api/subject-assigns
Content-Type: application/json

{
    "session_id": 1,
    "classes_id": 2,
    "section_id": 1,
    "subjects": [
        {
            "subject_id": 1,
            "staff_id": 5
        },
        {
            "subject_id": 2, 
            "staff_id": 8
        }
    ]
}
```

### Complex Queries

**Get complete class structure for a session:**
```http
GET /api/sessions/{sessionId}/class-structure
```

Response:
```json
{
    "session": {
        "id": 1,
        "name": "2024-2025",
        "classes": [
            {
                "id": 1,
                "name": "One",
                "sections": [
                    {
                        "id": 1,
                        "name": "A",
                        "subjects": [
                            {
                                "id": 1,
                                "name": "Mathematics",
                                "teacher": {
                                    "id": 5,
                                    "name": "Mr. Johnson"
                                }
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```

**Get teacher's assigned subjects:**
```http
GET /api/staff/{staffId}/subject-assignments?session_id=1
```

### Multi-language Support

**Get class with specific language:**
```http
GET /api/classes/{id}?locale=bn
```

**Create class with translations:**
```http
POST /api/classes
Content-Type: application/json

{
    "name": "One",
    "translations": {
        "en": "One",
        "bn": "এক",
        "ar": "واحد"
    }
}
```

---

## Implementation Best Practices

### 1. Setup Recommendations

**Academic Year Initialization:**
1. Create academic session first
2. Set up classes for the institution level
3. Create class setups to associate classes with sessions
4. Add section assignments based on enrollment projections
5. Create subjects once, reuse across sessions
6. Assign subjects to class-section combinations with teachers

**Data Validation:**
```php
// Always validate relationships exist
$request->validate([
    'session_id' => 'required|exists:sessions,id',
    'classes_id' => 'required|exists:classes,id',
    'section_id' => 'required|exists:sections,id'
]);
```

### 2. Performance Considerations

**Eager Loading:**
```php
// Load related data efficiently
$classSetups = ClassSetup::with([
    'session',
    'class.translations',
    'classSetupChildrenAll.section.translations'
])->where('session_id', $sessionId)->get();
```

**Scoped Queries:**
```php
// Use model scopes for common filters
$activeClasses = Classes::active()->get();
$activeSubjects = Subject::active()->where('type', SubjectType::THEORY)->get();
```

### 3. Multi-tenant Considerations

**Tenant Isolation:**
- All academic entities are automatically scoped to the current tenant
- Use tenant-aware foreign keys in all relationships
- Validate cross-tenant data access is prevented

**Session Context:**
```php
// Always work within a session context
class AcademicService {
    protected $currentSession;
    
    public function __construct() {
        $this->currentSession = Session::current();
    }
    
    public function getClassStructure() {
        return ClassSetup::where('session_id', $this->currentSession->id)
            ->with(['class', 'classSetupChildrenAll.section'])
            ->get();
    }
}
```

### 4. Data Integrity

**Cascade Deletion Protection:**
```php
// Check for dependent records before deletion
public function deleteClass($classId) {
    $class = Classes::findOrFail($classId);
    
    if ($class->classSetup()->exists()) {
        throw new \Exception('Cannot delete class with existing setups');
    }
    
    $class->delete();
}
```

**Status Management:**
```php
// Use soft status changes instead of hard deletion
public function deactivateClass($classId) {
    Classes::findOrFail($classId)->update(['status' => Status::INACTIVE]);
}
```

### 5. Translation Best Practices

**Fallback Strategy:**
```php
public function getTranslatedName($locale = null) {
    $locale = $locale ?? app()->getLocale();
    
    $translation = $this->translations()
        ->where('locale', $locale)
        ->first();
        
    return $translation ? $translation->name : $this->name;
}
```

**Bulk Translation Creation:**
```php
public function createWithTranslations($data, $translations) {
    $entity = $this->create($data);
    
    foreach ($translations as $locale => $translatedData) {
        $entity->translations()->create([
            'locale' => $locale,
            ...$translatedData
        ]);
    }
    
    return $entity;
}
```

### 6. Repository Pattern Implementation

**Example Academic Repository:**
```php
class ClassSetupRepository implements ClassSetupInterface {
    
    public function getSessionClasses($sessionId) {
        return ClassSetup::where('session_id', $sessionId)
            ->with(['class.translations', 'classSetupChildrenAll.section'])
            ->active()
            ->get();
    }
    
    public function assignSectionToClass($classSetupId, $sectionId) {
        return ClassSetupChildren::create([
            'class_setup_id' => $classSetupId,
            'section_id' => $sectionId
        ]);
    }
    
    public function getClassSections($classId, $sessionId) {
        $classSetup = ClassSetup::where('classes_id', $classId)
            ->where('session_id', $sessionId)
            ->first();
            
        return $classSetup ? 
            $classSetup->classSetupChildrenAll()->with('section')->get() : 
            collect();
    }
}
```

### 7. Testing Recommendations

**Unit Test Example:**
```php
public function test_class_setup_children_relationships() {
    $session = Session::factory()->create();
    $class = Classes::factory()->create();
    $section = Section::factory()->create();
    
    $classSetup = ClassSetup::create([
        'session_id' => $session->id,
        'classes_id' => $class->id
    ]);
    
    $child = ClassSetupChildren::create([
        'class_setup_id' => $classSetup->id,
        'section_id' => $section->id
    ]);
    
    $this->assertEquals($class->id, $child->class->id);
    $this->assertEquals($section->id, $child->section->id);
}
```

---

## Conclusion

The academic core system provides a robust foundation for managing educational institutions of varying sizes and complexity. The modular design supports growth from simple single-school deployments to complex multi-tenant SaaS platforms.

Key strengths of this system:
- **Flexibility**: Supports diverse academic structures
- **Scalability**: Multi-tenant architecture ready
- **Internationalization**: Built-in translation support
- **Data Integrity**: Strong relationship management
- **Extensibility**: Repository pattern for custom business logic

For additional support or custom implementation needs, refer to the Laravel documentation and the specific module documentation within the `Modules/MainApp` directory.

---

*Last updated: 2024-08-31*
*Version: 1.0*