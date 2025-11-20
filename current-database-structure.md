
## Packages Table Structure

| Field             | Type                                              | Null | Key | Default   | Extra          |
|-------------------|--------------------------------------------------|------|-----|-----------|----------------|
| id                | bigint unsigned                                  | NO   | PRI |           | auto_increment |
| payment_type      | enum('prepaid','postpaid')                       | NO   |     | prepaid   |                |
| name              | varchar(255)                                     | YES  |     |           |                |
| price             | decimal(16,2)                                    | NO   |     | 0.00      |                |
| per_student_price | decimal(16,2)                                    | NO   |     | 0.00      |                |
| student_limit     | int                                              | YES  |     |           |                |
| staff_limit       | int                                              | YES  |     |           |                |
| duration          | tinyint                                          | YES  |     |           |                |
| duration_number   | int                                              | YES  |     |           |                |
| description       | varchar(255)                                     | YES  |     |           |                |
| popular           | tinyint                                          | NO   |     | 0         |                |
| status            | tinyint                                          | NO   |     | 1         |                |
| created_at        | timestamp                                        | YES  |     |           |                |
| updated_at        | timestamp                                        | YES  |     |           |                |

**Notes**:
- `payment_type`: Indicates whether the package is prepaid or postpaid. Default is `prepaid`.
- `price`: The main package price (per branch, per billing period).
- `per_student_price`: Additional price per student, if any.
- `student_limit`: The maximum number of students allowed under the package (per branch).
- `staff_limit`: Maximum number of staff allowed.
- `duration`: Encodes the unit of the package's duration (e.g., 1=days, 2=months, 3=years; see system enum definition).
- `duration_number`: The number of units applied for the duration (e.g., 30 days or 12 months).
- `popular`: 1 if this package is marked as 'popular' in the UI.
- `status`: 1 for active/enabled, 0 for disabled/inactive.
- Timestamps track row creation and updates.


### Example Row

| id | payment_type | name           | price  | per_student_price | student_limit | staff_limit | duration | duration_number | description                          | popular | status | created_at           | updated_at           |
|----|--------------|----------------|--------|-------------------|--------------|-------------|----------|-----------------|--------------------------------------|---------|--------|----------------------|----------------------|
| 1  | prepaid      | Basic Package  | 25.00  | 0.00              | 430         | 100         | 2        | 12              | Default basic package for schools     | 0       | 1      | 2025-11-05 10:53:41  | 2025-11-15 23:23:10  |

**Legend for duration:**
- 1 = Days
- 2 = Months
- 3 = Years

**Interpretation:**  
- This package ("Basic Package") is prepaid, costs $25 per branch for 12 months, allows up to 430 students and 100 staff per branch.  
- All package prices in this table are **per branch** and for the specified billing period according to `duration` and `duration_number`.



## Schools Table Structure

| Field           | Type              | Null | Key  | Default   | Extra          |
|-----------------|-------------------|------|------|-----------|----------------|
| id              | bigint unsigned   | NO   | PRI  |           | auto_increment |
| sub_domain_key  | varchar(255)      | YES  |      |           |                |
| name            | varchar(255)      | YES  |      |           |                |
| package_id      | bigint unsigned   | NO   | MUL  |           |                |
| phone           | varchar(255)      | YES  |      |           |                |
| email           | varchar(255)      | YES  |      |           |                |
| address         | varchar(255)      | YES  |      |           |                |
| status          | tinyint           | NO   |      | 1         |                |
| created_at      | timestamp         | YES  |      |           |                |
| updated_at      | timestamp         | YES  |      |           |                |

**Notes**:
- `id`: Unique identifier for each school.
- `sub_domain_key`: Used to identify each school via subdomain (e.g., `myschool.app.com`).
- `package_id`: Foreign key referencing the selected package for the school.
- `status`: 1 = active; 0 = inactive.
- Timestamps track row creation and updates.
- Note:- the subdomain key is unused in our system.

### Current data

| id | sub_domain_key      | name               | package_id | phone          | email                   | address      | status | created_at           | updated_at           |
|----|---------------------|--------------------|------------|----------------|-------------------------|--------------|--------|----------------------|----------------------|
| 1  | noradin-schools     | Noradin Schools    | 1          | +1-000-0000-0000 | admin@mainschool.com  | Somaliland   | 1      | 2025-11-05 10:53:41  | 2025-11-05 10:53:41  |
| 2  | gacanlibaax         | Gacan Libaax       | 1          | 0633353722     | info@gacanlibax.com     | Hargeisa     | 1      | 2025-11-06 00:42:34  | 2025-11-08 01:13:18  |
| 3  | ilays-schools       | Ilays Schools      | 1          | 0634158322     | info@ilays.com          | Somaliland   | 1      | 2025-11-15 02:52:39  | 2025-11-15 02:58:16  |
| 4  | al-irshaad-schools  | Al-Irshaad Schools | 1          | 0634476798     | info@irshaadschools.com | Somaliland   | 1      | 2025-11-15 03:13:10  | 2025-11-16 05:58:41  |



## Subscription Table Structure

| Field           | Type                           | Null | Key  | Default   | Extra          |
|-----------------|--------------------------------|------|------|-----------|----------------|
| id              | bigint unsigned                | NO   | PRI  |           | auto_increment |
| payment_type    | enum('prepaid','postpaid')     | NO   |      | prepaid   |                |
| name            | varchar(255)                   | YES  |      |           |                |
| price           | int                            | YES  |      |           |                |
| student_limit   | int                            | YES  |      |           |                |
| staff_limit     | int                            | YES  |      |           |                |
| expiry_date     | date                           | YES  |      |           |                |
| trx_id          | varchar(255)                   | YES  |      |           |                |
| method          | varchar(255)                   | YES  |      |           |                |
| features_name   | longtext                       | YES  |      |           |                |
| features        | longtext                       | YES  |      |           |                |
| status          | tinyint                        | NO   |      | 0         |                |
| payment_status  | tinyint                        | NO   |      | 0         |                |
| created_at      | timestamp                      | YES  |      |           |                |
| updated_at      | timestamp                      | YES  |      |           |                |
| branch_id       | bigint unsigned                | NO   |      | 1         |                |
| school_id       | bigint unsigned                | YES  | MUL  |           |                |
| package_id      | bigint unsigned                | YES  | MUL  |           |                |

**Notes:**
- Each record represents a subscription instance for a branch under a school, tied to a specific package.
- `payment_type`: "prepaid" or "postpaid" (default: "prepaid").
- `price`: Cost of the subscription for the duration/branch.
- `student_limit`/`staff_limit`: Limits enforced by the selected package.
- `expiry_date`: When this subscription for a branch expires.
- `trx_id`: Reference/transaction identifier for the payment.
- `method`: Payment method used.
- `features_name`/`features`: Used to record feature sets as text, if needed.
- `status`: General status (0 = inactive, 1 = active).
- `payment_status`: Status of payment approval (e.g., 0 = pending, 1 = approved).
- `branch_id`: Branch to which this subscription instance belongs (**required**). which is technically wrong this subscription table should not have branch_id because that is school specific you might suggest better solution 
- `school_id`: School owning this branch.
- `package_id`: The package configuration this subscription is based on.
- `created_at`, `updated_at`: Timestamps for auditing/subscription events.

### Current Subscription Data

| id | payment_type | name        | price | student_limit | staff_limit | expiry_date  | trx_id                                 | method | features_name | features | status | payment_status | created_at           | updated_at           | branch_id | school_id | package_id |
|----|-------------|-------------|-------|--------------|-------------|-------------|----------------------------------------|--------|---------------|----------|--------|---------------|----------------------|----------------------|-----------|-----------|------------|
| 1  | prepaid     | basic plan  | 80    | 1000         | 100         | 2025-11-30  |                                        | cash   |               |          | 1      | 1             | 2025-06-03 10:04:08 | 2025-06-03 10:04:08  | 1         | 1         | 1          |
| 2  | prepaid     |             | 80    | 1000         | 100         | 2025-11-20  |                                        | cash   | []            | []       | 1      | 1             | 2025-11-08 03:13:31 | 2025-11-08 03:13:31  | 1         | 2         | 1          |
| 3  | prepaid     |             | 80    | 1000         | 100         | 2026-11-15  | e7f312a4-758a-4498-9f0a-438d8c73e73e   |        | []            | []       | 0      | 0             | 2025-11-15 02:52:39 | 2025-11-15 02:52:39  | 1         | 3         | 1          |
| 4  | prepaid     |             | 80    | 1000         | 100         | 2026-11-15  | d0f96441-5adf-4157-88f5-926428177deb   |        | []            | []       | 0      | 0             | 2025-11-15 03:13:10 | 2025-11-15 03:13:10  | 1         | 4         | 1          |












