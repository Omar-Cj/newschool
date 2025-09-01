# Student Registration System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Complete Field Requirements](#complete-field-requirements)
3. [Database Schema](#database-schema)
4. [Registration Form Fields](#registration-form-fields)
5. [Admin Dashboard Navigation](#admin-dashboard-navigation)
6. [Data Dependencies & Relationships](#data-dependencies--relationships)
7. [Technical Implementation](#technical-implementation)
8. [Validation Rules](#validation-rules)

---

## System Overview

The Student Registration System is part of a comprehensive Laravel-based School Management System that supports both multi-tenant SaaS deployment and single-school installations. The system uses a modular architecture with `nwidart/laravel-modules` and implements comprehensive student data management with parent/guardian relationships.

**Key Features:**
- Complete student profile management
- Parent/Guardian relationship tracking
- Academic assignment (class, section, shift)
- Document upload capabilities
- User account creation integration
- Multi-language support
- Permission-based access control

---

## Complete Field Requirements

### 1. **Basic Information (Required Fields)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `admission_no` | string/number | ✅ | Unique admission number |
| `roll_no` | number | ✅ | Roll number within class |
| `first_name` | string | ✅ | Student's first name |
| `last_name` | string | ✅ | Student's last name |
| `date_of_birth` | date | ✅ | Student's date of birth |
| `admission_date` | date | ✅ | Date of admission to school |
| `class` | select | ✅ | Academic class assignment |
| `section` | select | ✅ | Class section assignment |
| `parent` | select | ✅ | Parent/Guardian selection |
| `department_id` | select | ✅ | Department assignment |
| `status` | select | ✅ | Active/Inactive status |

### 2. **Contact Information (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `mobile` | number | ❌ | Student's mobile number |
| `email` | email | ❌ | Student's email address |
| `emergency_contact` | string | ❌ | Emergency contact information |

### 3. **Personal Details (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `student_ar_name` | string | ❌ | Student name in Arabic |
| `gender` | select | ❌ | Gender selection |
| `religion` | select | ❌ | Religious affiliation |
| `blood_group` | select | ❌ | Blood group |
| `category` | select | ❌ | Student category |
| `shift` | select | ❌ | School shift timing |
| `image` | file | ❌ | Student photograph (100x100 px) |

### 4. **Geographic & Identity Information (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `place_of_birth` | string | ❌ | Place where student was born |
| `nationality` | string | ❌ | Student's nationality |
| `cpr_no` | string | ❌ | CPR/ID number |
| `student_id_certificate` | string | ❌ | ID certificate number |
| `spoken_lang_at_home` | string | ❌ | Primary language spoken at home |
| `residance_address` | string | ❌ | Current residence address |

### 5. **Family Information (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `health_status` | string | ❌ | Current health status |
| `rank_in_family` | number | ❌ | Birth order in family (default: 1) |
| `siblings` | number | ❌ | Number of brothers/sisters (default: 0) |
| `siblings_discount` | boolean | ❌ | Sibling discount eligibility |

### 6. **Academic History (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `previous_school` | checkbox | ❌ | Previously attended another school |
| `previous_school_info` | textarea | ❌ | Previous school information |
| `previous_school_image` | file | ❌ | Previous school documents |

### 7. **Account Information (Optional)**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `username` | string | ❌ | Login username (must be unique) |
| `password_type` | radio | ❌ | Default (123456) or Custom |
| `password` | string | ❌ | Custom password (min 6 chars) |

### 8. **Document Management**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `upload_documents` | array | ❌ | Multiple document uploads with names |
| `document_names.*` | string | ❌ | Document name/description |
| `document_files.*` | file | ❌ | Document file uploads |

---

## Database Schema

### Students Table Structure
```sql
-- Primary students table (created: 2023-02-24)
CREATE TABLE students (
    id BIGINT PRIMARY KEY,
    admission_no VARCHAR(255) NULL,
    roll_no INT NULL,
    first_name VARCHAR(255) NULL,
    last_name VARCHAR(255) NULL,
    mobile VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    dob DATE NULL,
    admission_date DATE NULL,
    
    -- Foreign Keys
    student_category_id BIGINT NULL,
    religion_id BIGINT NULL,
    blood_group_id BIGINT NULL,
    gender_id BIGINT NULL,
    category_id BIGINT NULL,
    image_id BIGINT NULL,
    parent_guardian_id BIGINT NULL,
    user_id BIGINT NULL,
    department_id BIGINT NULL,
    previous_school_image_id BIGINT NULL,
    
    -- Document storage
    upload_documents LONGTEXT NULL, -- JSON array
    
    -- Status & Settings
    status TINYINT DEFAULT 1, -- Active/Inactive
    siblings_discount TINYINT DEFAULT 0,
    
    -- Academic History
    previous_school TINYINT DEFAULT 0,
    previous_school_info TEXT NULL,
    
    -- Personal Details
    health_status VARCHAR(255) NULL,
    rank_in_family INT DEFAULT 1,
    siblings INT DEFAULT 0,
    
    -- Location & Identity
    place_of_birth VARCHAR(255) NULL,
    nationality VARCHAR(255) NULL,
    cpr_no VARCHAR(255) NULL,
    spoken_lang_at_home VARCHAR(255) NULL,
    residance_address VARCHAR(255) NULL,
    
    -- Additional fields (added: 2024-08-30)
    student_ar_name VARCHAR(255) NULL,
    student_id_certificate VARCHAR(255) NULL,
    emergency_contact VARCHAR(255) NULL,
    student_code VARCHAR(255) NULL,
    
    -- Sibling discount (added: 2025-05-14)
    -- siblings_discount field enhanced
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Related Tables

#### Parent Guardians Table
```sql
CREATE TABLE parent_guardians (
    id BIGINT PRIMARY KEY,
    user_id BIGINT, -- FK to users table
    
    -- Father Information
    father_name VARCHAR(255) NULL,
    father_mobile VARCHAR(255) NULL,
    father_profession VARCHAR(255) NULL,
    father_image VARCHAR(255) NULL,
    father_nationality VARCHAR(255) NULL,
    father_id VARCHAR(255) NULL, -- Added 2024-08-30
    
    -- Mother Information
    mother_name VARCHAR(255) NULL,
    mother_mobile VARCHAR(255) NULL,
    mother_profession VARCHAR(255) NULL,
    mother_image VARCHAR(255) NULL,
    mother_id VARCHAR(255) NULL, -- Added 2024-08-30
    
    -- Guardian Information
    guardian_name VARCHAR(255) NULL,
    guardian_email VARCHAR(255) NULL,
    guardian_mobile VARCHAR(255) NULL,
    guardian_image VARCHAR(255) NULL,
    guardian_profession VARCHAR(255) NULL,
    guardian_relation VARCHAR(255) NULL,
    guardian_address VARCHAR(255) NULL,
    guardian_place_of_work VARCHAR(255) NULL,
    guardian_position VARCHAR(255) NULL,
    
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Supporting Tables
- `users` - System user accounts
- `genders` - Gender options
- `religions` - Religious affiliations
- `blood_groups` - Blood type options
- `student_categories` - Student categorization
- `departments` - School departments
- `uploads` - File management
- `shifts` - School timing shifts

---

## Registration Form Fields

### Form Location
**File:** `resources/views/backend/student-info/student/create.blade.php`
**Route:** `GET /student/create` (Name: `student.create`)
**Controller:** `App\Http\Controllers\StudentInfo\StudentController@create`

### Field Layout (4-column responsive grid)

#### Row 1: Basic Student Information
```html
<!-- Column 1 --> Admission No* (number input)
<!-- Column 2 --> Roll No* (number input)  
<!-- Column 3 --> First Name* (text input)
<!-- Column 4 --> Last Name* (text input)
```

#### Row 2: Contact & Identity
```html
<!-- Column 1 --> Arabic Name (text input)
<!-- Column 2 --> Mobile (number input)
<!-- Column 3 --> Email (email input)
<!-- Column 4 --> Username (text input)
```

#### Row 3: Academic Assignment
```html
<!-- Column 1 --> Department* (select dropdown)
<!-- Column 2 --> Class* (select dropdown)
<!-- Column 3 --> Section* (select dropdown - populated via AJAX)
<!-- Column 4 --> Shift (select dropdown)
```

#### Row 4: Personal Details
```html
<!-- Column 1 --> Date of Birth* (date input)
<!-- Column 2 --> Religion (select dropdown)
<!-- Column 3 --> Gender (select dropdown)
<!-- Column 4 --> Category (select dropdown)
```

#### Row 5: Additional Details
```html
<!-- Column 1 --> Blood Group (select dropdown)
<!-- Column 2 --> Admission Date* (date input)
<!-- Column 3 --> Student Image (file upload - 100x100px)
<!-- Column 4 --> Parent/Guardian* (select dropdown)
```

#### Dynamic Sibling Information Section
- Displays when parent is selected
- Shows existing children information
- Calculates automatic sibling discounts

#### Previous School Information (Conditional)
```html
<!-- Checkbox --> Attended School Previously
<!-- Textarea --> Previous School Info (shows if checked)
<!-- File Upload --> Previous School Documents (shows if checked)
```

#### Personal & Geographic Information
```html
<!-- Row --> Place of Birth | Nationality | CPR Number | Home Language
<!-- Row --> Residence Address | Status* | ID Certificate | Emergency Contact
```

#### Health & Family Information
```html
<!-- Row --> Health Status | Rank in Family | Number of Siblings | Password Type
```

#### Account Setup
```html
<!-- Radio --> Default Password (123456) | Custom Password
<!-- Input --> Custom Password Field (conditional)
```

#### Document Management Section
- Dynamic table for uploading multiple documents
- Add/Remove document rows functionality
- Document name and file input pairs

---

## Admin Dashboard Navigation

### Accessing Student Registration

#### Main Menu Path:
```
Dashboard → Student Info → Students → Add New
```

#### Direct URL Routes:
- **Student List:** `/student` (student.index)
- **Create Student:** `/student/create` (student.create)
- **Store Student:** `POST /student/store` (student.store)
- **Edit Student:** `/student/edit/{id}` (student.edit)
- **View Student:** `/student/show/{id}` (student.show)

#### Permission Requirements:
- **View Students:** `student_read` permission
- **Create Students:** `student_create` permission
- **Edit Students:** `student_update` permission
- **Delete Students:** `student_delete` permission

#### Additional Features Available:
1. **Import Students:** `/student/import` - Bulk student import
2. **Search Students:** `/student/search` - Advanced search
3. **Student Categories:** `/student/category` - Manage categories
4. **Parent Management:** `/parent` - Manage parents/guardians
5. **Promote Students:** `/promote/students` - Class promotion
6. **Disabled Students:** `/disabled/students` - Inactive students

### Related Data Management Locations

#### Parent/Guardian Management:
```
Dashboard → Student Info → Parents → Add New
```

#### Academic Setup:
```
Dashboard → Academic → Classes (manage classes)
Dashboard → Academic → Sections (manage sections)
Dashboard → Academic → Departments (manage departments)
Dashboard → Academic → Shifts (manage shifts)
```

#### System Configuration:
```
Dashboard → System Settings → Genders
Dashboard → System Settings → Religions  
Dashboard → System Settings → Blood Groups
Dashboard → Student Info → Student Categories
```

---

## Data Dependencies & Relationships

### Required Pre-setup Data

#### 1. Academic Structure
- **Departments:** Must exist before student creation
- **Classes:** Academic classes must be configured
- **Sections:** Class sections must be assigned
- **Shifts:** School timing shifts (optional)

#### 2. Personal Information Options
- **Genders:** Male, Female, Other options
- **Religions:** Religious denomination options
- **Blood Groups:** A+, A-, B+, B-, AB+, AB-, O+, O-
- **Student Categories:** Classification categories

#### 3. Parent/Guardian Information
- **Parent/Guardian Records:** Must exist or be created first
- **User Accounts:** System user for parent portal access

### Model Relationships

#### Student Model Relationships
```php
// Core relationships
belongsTo(User::class) // System user account
belongsTo(ParentGuardian::class) // Parent/Guardian
belongsTo(Department::class) // Academic department
belongsTo(SessionClassStudent::class) // Class assignment

// Personal information
belongsTo(Gender::class)
belongsTo(Religion::class)  
belongsTo(BloodGroup::class)
belongsTo(StudentCategory::class)
belongsTo(Shift::class)
belongsTo(Upload::class, 'image_id') // Profile photo

// Academic relationships
hasManyThrough(Staff::class, SubjectAssignChildren::class)
hasMany(FeesAssignChildren::class) // Fee assignments
hasMany(FeesCollect::class) // Fee payments
hasManyThrough(FeesMaster::class, FeesAssignChildren::class)

// Communication & tracking
hasOne(Message::class, 'sender_id', 'user_id') // Last message
hasMany(Message::class, 'receiver_id', 'user_id') // Unread messages
hasOne(StudentRouteEnrollment::class) // Vehicle tracking
```

#### ParentGuardian Model Relationships
```php
belongsTo(User::class) // System user account
hasMany(Student::class, 'parent_guardian_id') // Children
```

### Data Flow Process

#### Student Registration Flow:
1. **Parent/Guardian Creation** (if new)
   - Create user account for parent portal
   - Store parent/guardian personal information
   
2. **Student Information Entry**
   - Basic student details
   - Academic assignment (class, section, department)
   - Personal information (gender, religion, etc.)
   - Link to parent/guardian
   
3. **User Account Creation** (if username provided)
   - Create system user for student portal
   - Assign default or custom password
   - Link user account to student record
   
4. **Document Management**
   - Upload student documents
   - Store document metadata
   - Link documents to student record
   
5. **Academic Assignment**
   - Assign to class and section
   - Link to current academic session
   - Set up fee structures
   
6. **Sibling Discount Calculation** (automatic)
   - Check existing siblings
   - Apply eligible discounts
   - Update fee structures

---

## Technical Implementation

### Controller: StudentController

#### Key Methods:
```php
// Display student registration form
public function create()
{
    // Load dropdown data: classes, departments, shifts, etc.
    // Return create view with all required options
}

// Store new student
public function store(StudentStoreRequest $request)  
{
    // Validate input via StudentStoreRequest
    // Process student creation via StudentRepository
    // Handle file uploads and document management
    // Create user account if required
    // Return success/error response
}

// Display student list
public function index()
{
    // Get paginated students list
    // Apply filters and search
    // Return students index view
}

// Search students
public function search(Request $request)
{
    // Apply search filters
    // Return filtered results
}
```

#### Repository Pattern:
- **StudentRepository:** Handles all database operations
- **ParentGuardianRepository:** Parent/Guardian management  
- **Academic Repositories:** Classes, Sections, Departments
- **System Repositories:** Genders, Religions, Blood Groups

#### Service Layer Integration:
- File upload handling
- User account creation
- Notification services
- Fee calculation services

### Routes Configuration

#### File: `routes/student_info.php`
```php
// Main student management routes
Route::controller(StudentController::class)->prefix('student')->group(function () {
    Route::get('/', 'index')->name('student.index');
    Route::get('/create', 'create')->name('student.create');
    Route::post('/store', 'store')->name('student.store');
    Route::get('edit/{id}', 'edit')->name('student.edit');
    Route::get('show/{id}', 'show')->name('student.show');
    Route::put('update', 'update')->name('student.update');
    Route::delete('/delete/{id}', 'delete')->name('student.delete');
    
    // Additional functionality
    Route::get('/import', 'import')->name('student.import');
    Route::post('/import-submit', 'importSubmit')->name('student.importSubmit');
    Route::get('/get-children/{parentId}', 'getChildren')->name('student.getChildren');
});
```

#### Middleware Stack:
- **Tenancy:** Multi-tenant support
- **Authentication:** User login required  
- **Authorization:** Permission-based access
- **Feature Check:** Module activation check
- **XSS Protection:** Input sanitization
- **Demo Mode:** Prevents modifications in demo

---

## Validation Rules

### StudentStoreRequest Validation

#### Required Fields:
```php
'admission_no' => 'required|max:255|unique:students,admission_no',
'roll_no' => 'required|max:255',
'first_name' => 'required|max:255', 
'last_name' => 'required|max:255',
'department_id' => 'required|exists:departments,id',
'class' => 'required|max:255',
'section' => 'required|max:255', 
'date_of_birth' => 'required|max:255',
'admission_date' => 'required|max:255',
'parent' => 'required|max:255',
'status' => 'required|max:255'
```

#### Conditional Validation:
```php
// Mobile - only validated if provided
'mobile' => 'max:255|unique:users,phone' // (if not empty)

// Email - only validated if provided  
'email' => 'max:255|unique:users,email' // (if not empty)

// Username - must be unique if provided
'username' => 'unique:users,username',

// Password - minimum 6 characters if custom
'password' => 'min:6'
```

#### Optional Fields:
```php
'health_status' => 'nullable|max:255',
'rank_in_family' => 'nullable|max:20',
'siblings' => 'nullable|max:20',
'siblings_discount' => 'nullable'
```

### Database Constraints:

#### Unique Constraints:
- `admission_no` - Must be unique across students
- `email` - Must be unique in users table
- `mobile` - Must be unique in users table (phone field)
- `username` - Must be unique in users table

#### Foreign Key Constraints:
- `parent_guardian_id` → `parent_guardians.id`
- `user_id` → `users.id`
- `department_id` → `departments.id`
- `religion_id` → `religions.id`
- `blood_group_id` → `blood_groups.id`
- `gender_id` → `genders.id`
- `category_id` → `student_categories.id`
- `image_id` → `uploads.id`

#### Business Logic Validation:
- Parent/Guardian must exist before student creation
- Academic session must be active
- Department must be active and available
- Class and Section must be properly assigned
- File uploads must meet size and type requirements

---

## Summary

This Student Registration System provides a comprehensive solution for managing student information within a school management context. The system handles:

- **Complete student profiles** with 50+ data fields
- **Parent/Guardian relationship management**  
- **Academic assignment and tracking**
- **Document management and uploads**
- **User account integration**
- **Sibling discount calculations**
- **Multi-language support**
- **Permission-based access control**

The system is designed to be flexible, supporting both individual student registration and bulk import functionality, while maintaining data integrity through comprehensive validation and relationship management.

### Key Access Points for Administrators:

1. **Student Registration:** `Dashboard → Student Info → Students → Add New`
2. **Parent Management:** `Dashboard → Student Info → Parents → Add New`  
3. **Academic Setup:** `Dashboard → Academic → [Classes/Sections/Departments]`
4. **System Configuration:** `Dashboard → System Settings → [Basic Data]`

All functionality is protected by role-based permissions and supports the school's operational workflow from registration through graduation.