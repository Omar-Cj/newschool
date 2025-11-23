
/*
## 🔄 Globalizing academic_level_configs Table

### Goal
Make the `academic_level_configs` table **global** by removing the `school_id` column, so that academic levels are systemwide and not scoped per school. This ensures when a class is created, its academic level can be automatically recognized/configured everywhere.

---

### 1. **Table Structure Update**

**Remove** the `school_id` column from the table definition (and indexes), so it becomes:

id,bigint unsigned,NO,PRI,,auto_increment
school_id,bigint unsigned,NO,MUL,1,""
academic_level,"enum('primary','secondary','high_school','kg')",NO,MUL,,""
display_name,varchar(100),NO,"",,""
description,text,YES,"",,""
class_identifiers,json,NO,"",,""
numeric_range,json,YES,"",,""
sort_order,int,NO,"",0,""
is_active,tinyint(1),NO,MUL,1,""
auto_assign_mandatory_services,tinyint(1),NO,"",1,""
created_by,bigint unsigned,YES,MUL,,""
updated_by,bigint unsigned,YES,MUL,,""
created_at,timestamp,YES,"",,""
updated_at,timestamp,YES,"",,""

> **NOTE**: No `school_id` ! All data is now global/system-wide.

---

### 2. **Data Migration/Seeding**

Here is the **initial global seed data** for `academic_level_configs` (without `school_id`):

| id | academic_level | display_name    | description                  | class_identifiers                                                                                                                                         | numeric_range                      | sort_order | is_active | auto_assign_mandatory_services | created_by | updated_by | created_at           | updated_at           |
|----|---------------|-----------------|------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------|------------|-----------|-------------------------------|------------|------------|----------------------|----------------------|
| 1  | kg            | Kindergarten    | Kindergarten students (KG-1 to KG-3) | ["KG", "KG-1", "KG-2", "KG-3", "PreK", "Pre-K", "Nursery", "Pre-School"]       | {"max": 0, "min": -3}              | 1          | 1         | 1                             | null       | null       | 2025-09-09 11:34:25  | 2025-09-11 07:19:01  |
| 2  | primary       | Primary School  | Primary education levels (Grade 1 to Grade 8) | ["1", "2", "3", "4", "5", "6", "7", "8", "Class 1", "Class 2", ...]      | {"max": 8, "min": 1}               | 2          | 1         | 1                             | null       | null       | 2025-09-09 11:34:25  | 2025-09-11 07:19:01  |
| 3  | secondary     | Secondary School| Secondary education levels (Form 1 to Form 4) | ["Form 1", "Form 2", "Form 3", "Form 4", "F1", "F2", "F3", "F4"]         | {"max": 104, "min": 101}           | 3          | 1         | 1                             | null       | null       | 2025-09-09 11:34:25  | 2025-09-11 07:19:01  |
| 4  | high_school   | High School     | High school levels (if applicable)    | ["11", "12", "Class 11", "Class 12", "Grade 11", "Grade 12"]              | {"max": 12, "min": 11}             | 4          | 1         | 1                             | null       | null       | 2025-09-09 11:34:25  | 2025-09-11 07:19:01  |

#### SQL Seed Example:
```sql
INSERT INTO academic_level_configs
    (academic_level, display_name, description, class_identifiers, numeric_range, sort_order, is_active, auto_assign_mandatory_services, created_at, updated_at)
VALUES
    ('kg', 'Kindergarten', 'Kindergarten students (KG-1 to KG-3)', '["KG","KG-1","KG-2","KG-3","PreK","Pre-K","Nursery","Pre-School"]', '{"max":0,"min":-3}', 1, 1, 1, '2025-09-09 11:34:25', '2025-09-11 07:19:01'),
    ('primary', 'Primary School', 'Primary education levels (Grade 1 to Grade 8)', '["1","2","3","4","5","6","7","8","Class 1","Class 2","Class 3","Class 4","Class 5","Class 6","Class 7","Class 8","Grade 1","Grade 2","Grade 3","Grade 4","Grade 5","Grade 6","Grade 7","Grade 8"]', '{"max":8,"min":1}', 2, 1, 1, '2025-09-09 11:34:25', '2025-09-11 07:19:01'),
    ('secondary', 'Secondary School', 'Secondary education levels (Form 1 to Form 4)', '["Form 1","Form 2","Form 3","Form 4","F1","F2","F3","F4"]', '{"max":104,"min":101}', 3, 1, 1, '2025-09-09 11:34:25', '2025-09-11 07:19:01'),
    ('high_school', 'High School', 'High school levels (if applicable)', '["11","12","Class 11","Class 12","Grade 11","Grade 12"]', '{"max":12,"min":11}', 4, 1, 1, '2025-09-09 11:34:25', '2025-09-11 07:19:01');
```

---

### 3. **Automatic Academic Level Configuration**

**Implementation Note:**  
When creating a class, look up its associated academic level (e.g. "Form 1" matches "secondary", "KG-2" matches "kg") using the `class_identifiers` in the **global** `academic_level_configs`.  
This means all new classes automatically know their level systemwide, regardless of school.





#### 2.x. **Role-Permission Seeding**

Below are the insert statements to seed permissions for the three roles as required.  
Assume the following mapping:

- **role_id = 1:** regular Admin (all management permissions)
- **role_id = 4:** Accountant (limited to fees, students, accounts)
- **role_id = 5:** Teacher (attendance-create, exam entry only)

---

##### **Journals Permission**
> _Note: This permission does not exist. Add it as:_


---



---

#### **Admin (`role_id = 1`):**
Can manage everything below:

- student_info: student, student_category, promote_students, disabled_students, parent, admission
- academic: classes, section, shift, class_setup, subject, subject_assign, class_routine, time_schedule, class_room
- attendance: attendance, attendance_report
- fees: fees_type, fees_collect, fees_generation, due_fees, fees_collection, report_fees_collection
- examination: exam_type, marks_grade, exam_routine, exam_setting, homework, exam_entry
- Transportation: bus
- Accounts: expense_category, expense, account, cash_transfer, journals
- Report: report_center, student_reports_read
- language: language
- staff:  users
- settings: general_settings, sessions,etc.



#### **Accountant (`role_id = 4`):**
Should have access to:
- fees (fees_type, fees_collect, fees_generation,  fees_collection,)
- student (student)
- accounts: expense_category, expense, account, cash_transfer(except Approve,Reject), journals


```

---

#### **Teacher (`role_id = 5`):**
Should have only:
- attendance ("attendance" with create, update)
- examination (exam_entry: only create/update for exams)




**Possible Cause and Suggestion for the Bus Area Not Being Optional in Student Registration**

There are a few common reasons why you might encounter this issue even though the database column for "bus" (or `bus_id`, `transport_id`, etc.) in the student table allows `NULL` or is otherwise optional:

1. **Form Validation on Backend:**  
   - There is likely backend validation (e.g., in a Laravel `FormRequest` or in the Controller) that marks the bus field as `required` when saving or updating a student.
   - Check your `StudentRequest` (or similarly named request class) for a rule like `'bus_id' => 'required'`.  
   - If you want the field to be optional, ensure it's `'nullable'` or just omitted from required validation:
     ```php
     // In app/Http/Requests/StudentRequest.php or similar
     public function rules()
     {
         return [
             // ...
             'bus_id' => 'nullable|exists:buses,id',
             // ...
         ];
     }
     ```

2. **Frontend/Blade Form Marking "bus" as Required:**  
   - On the student registration form (`resources/views/student/create.blade.php` or similar), ensure the `<select>` or `<input>` for bus is _not_ marked as `required` in the HTML.
     ```html
     <!-- Remove required attribute -->
     <select name="bus_id" class="form-control">
         <option value="">-- None --</option>
         @foreach($buses as $bus)
             <option value="{{ $bus->id }}">{{ $bus->name }}</option>
         @endforeach
     </select>
     ```

3. **Controller Logic:**  
   - When you store the student, make sure you are not setting the bus field in a way that would violate the database constraints. E.g., if `bus_id` is `null` or an empty string, it should not cause a failure.  
   - Example:
     ```php
     $student->bus_id = $request->input('bus_id') ?: null;
     ```

4. **Database Default Value:**  
   - Confirm in your migration that the `bus_id` (or similarly named) column is **nullable**:
     ```php
     $table->unsignedBigInteger('bus_id')->nullable();
     ```

**Summary/Troubleshooting Steps:**
- Review **form validation rules** for the student registration.
- Review the **student registration form** for `required` attributes.
- Check the **controller logic** for how the field is stored.
- Double-check your **migration** for the column's nullability.

---

**If you provide your specific controller method and request/validation rules, I can pinpoint the exact line to change.**








