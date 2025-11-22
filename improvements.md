/*
# 📦 School Creation Improvement: Required Branch Data Seeding

## Objective

When creating a new **school**, the system should automatically seed essential master data for **each newly created branch**. This ensures each branch is ready-to-use with minimum required configuration for immediate operation.

---

## 📝 Data To Seed For *Each* Branch

Below is the set of records that must be automatically created **per branch** upon school/branch creation:

### 1. Student Categories
- **Normal**  
    - `fee_exempt`: false
- **Scholarship**  
    - `fee_exempt`: true

> Table: `student_categories`
> 
> | id | school_id | branch_id | name        | fee_exempt | ... |
> |----|-----------|-----------|-------------|------------|-----|
> | *  | {new}     | {branch}  | Normal      | 0          |     |
> | *  | {new}     | {branch}  | Scholarship | 1          |     |

---

### 2. Fee Types

Seed the following "fee types" for *each branch*, using the new `school_id` and that specific `branch_id`:

| id | school_id | branch_id | code                   | name                | description       | academic_level | amount | ... | category | status | ... |
|----|-----------|-----------|------------------------|---------------------|-------------------|---------------|--------|-----|----------|--------|-----|
| 1  | {new}     | {branch}  | Full Tution Fee Secondary | Secondary Tuition   | Lacagta Bisha ardayda Secondaryga | secondary     | 30.00  | ... | academic | 1 | ... |
| 2  | {new}     | {branch}  | Bus Fee                | Bus                 | Lacagta Baska     | all           | 15.00  | ... | transport | 1 | ... |
| 3  | {new}     | {branch}  | Full Tution Fee Primary | Primary Tuition     | Lacagta Bisha Primaryga | primary      | 15.00  | ... | academic | 1 | ... |
| 4  | {new}     | {branch}  | Full Tution Fee KG     | KG Tuition          | Lacagta Bisha ardayda KG | kg           | 15.00  | ... | academic | 1 | ... |

#### Fee-type CSV-style reference:
```
1,1,Full Tution Fee Secondary,Secondary Tuition,Lacagta Bisha ardayda Secondaryga,secondary,30.00,30,1,academic,1,2025-09-01 08:01:27,2025-09-10 11:10:15,1
2,1,Bus Fee,Bus,Lacagta Baska,all,15.00,30,0,transport,1,2025-09-02 03:46:06,2025-09-10 11:10:15,1
3,1,Full Tution Fee Primary,Primary Tuition,Lacagta Bisha Primaryga,primary,15.00,50,1,academic,1,2025-09-10 03:08:12,2025-09-10 11:10:15,1
4,1,Full Tution Fee KG,KG Tuition,Lacagta Bisha ardayda KG,kg,15.00,30,1,academic,1,2025-09-10 03:09:24,2025-09-10 11:10:15,1
```
- **Notes:** `school_id` and `branch_id` should be the *newly created* ones for each row.

---

### 3. Academic Sessions

For every branch, create an **academic session** where:
- `name`: current year (`YYYY`)
- `start_date`: `YYYY-01-01`
- `end_date`: `YYYY-12-31`
- `status`: active (1)
- Timestamps: `now()`

> Example row:
> 
> `id, school_id, branch_id, name, start_date, end_date, status, created_at, updated_at, created_by`
> 
> `*, {new}, {branch}, 2025, 2025-01-01, 2025-12-31, 1, now, now, {admin}`

---

### 4. Sections

For each session on the branch, seed default sections:
- **A**
- **B**
- **C**

---

### 5. Shifts

Seed the following shift records for the branch:
- **Morning**
- **Afternoon**

---

## 💡 Implementation Notes

- All data is to be seeded **per branch** immediately after the branch record is created, during school registration.
- Make sure to use the **correct new `school_id` and `branch_id` for each insertion**.
- All timestamps (e.g., `created_at`, `updated_at`) should use `now()` unless otherwise needed.
- Academic session "name" must always be current year in `YYYY` format (generate dynamically).

---

### 🚦 Summary - Automated Seeding Per Branch:

- [x] Student Categories: Normal & Scholarship (with correct `fee_exempt`)
- [x] Fee Types: 4 default items as shown above
- [x] Academic Session: current year, correct dates
- [x] Sections: A, B, C per session
- [x] Shifts: Morning & Afternoon

This will ensure every new branch is ready with base configuration for immediate use.
*/
