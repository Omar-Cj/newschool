# School Management System Enhancement Specification
## Package, Branch Management & Subscription System

### Version 1.0
### Date: November 2025

---

## 1. Executive Summary

This specification outlines the enhancement requirements for this school management system's package management,branch handling, and subscription billing features. The system is designed as a multi-tenant SaaS platform where schools can have multiple branches operating under a single subscription package with shared student limits.

---

## 2. Package Management Enhancement

### 2.1 Student Limit Implementation

#### 2.1.1 Package Configuration
- **Field**: `student_limit` (Integer)
- **Description**: Defines the maximum number of students allowed per branch for schools subscribing to this package
- **Application**: The limit applies individually to each branch under the school

- Note:- already in our main dashboard/multitenant dashboard located in @Modules/MainApp when creating packages we have student limit field we just want to make it work.

#### 2.1.2 Limit Enforcement Logic

```
For each school with Package X (student_limit = 430):
  - Branch A: Can have up to 430 students
  - Branch B: Can have up to 430 students
  - Branch N: Can have up to 430 students
```

#### 2.1.3 Validation Rules
- **Check Point**: During student registration/enrollment
- **Validation Query**: `COUNT(students) WHERE branch_id = {current_branch} AND status = 'active'`
- **Condition**: If count >= package.student_limit, block registration

#### 2.1.4 User Experience
- **Error Message**: Display modal/popup when limit is reached (sweetalert of this system patterns)
- **Message Content**: 
  ```
  "Your current package allows a maximum of [X] students per branch. 
  You have reached this limit. To enroll more students, please upgrade 
  to a higher package."
  ```




## 3. Branch Management System

### 3.1 School Creation Process

#### 3.1.1 Branch Allocation During School Registration

Currently, in the main dashboard, when a school is created, only a single branch is automatically generated for that school by default. However, as per the new requirements, school creation should allow specifying how many branches to create for the new school.

- **UI Change**: Add a field during school creation to allow the admin to select or enter the number of branches to create for the school.
    - The field must be named `number_of_branches` (Integer, Required).
    - **Default Value**: 1
    - **Minimum**: 1
- **Behavior Change**: On school creation, generate the specified number of branches associated with the new school, instead of only one.
    - For example, if `number_of_branches = 3`, three branches should be created and linked to the school on creation.




### 3.2 Branch Management Restrictions

#### 3.2.1 School Dashboard Modifications
- **Hide/Disable**: "Create New Branch" button in school-specific dashboard
- **Allowed Actions**: 
  - ✅ Edit existing branch details
  - ✅ View branch information
  - ❌ Create new branches
  - ❌ Delete branches

#### 3.2.2 School Dashboard Branch Dropdown Visibility Issue

**Issue:**  
In the school-specific dashboard, the top bar includes a dropdown menu for choosing between branches. Currently, this dropdown is not visible, preventing school super admin users from switching or viewing other branches from the dashboard.

**Requirement (Fix):**  
- Ensure that the branch selection dropdown is always visible in the top bar of the school-specific dashboard for all users who h

**Acceptance Criteria:**  
- The branch dropdown is visible and usable in the top bar of the school-specific dashboard for all school super admins (with role_id = 1)
- Users can see and select between available branches.
- Changing the branch in the dropdown updates the displayed data as expected.


## 4. Subscription Management System

### 4.1 Billing Cycle Configuration

#### 4.1.1 Subscription Parameters and Billing Logic

- **Billing Period**: The subscription billing period must be dynamically set based on the duration specified in the selected package. Supported durations include *days*, *monthly*, and *yearly*.
    - For example: 
      - *Package 1*: Duration = Monthly, Price = $25 → Billing Period = 30 days
      - *Package 2*: Duration = Yearly, Price = $250 → Billing Period = 365 days
      - *Package 3*: Duration = 15 days, Price = $10 → Billing Period = 15 days
- **Grace Period**: A standard grace period of 2 days will be applied after the subscription's expiry date, regardless of package duration.
- **Auto-renewal**: Optional. This setting can be configured on a per-school basis.

#### 4.1.2 Per-Branch Pricing and Student Limits

- **Package Pricing per Branch**: The listed package price applies **to each branch** under a school, not to the school as a whole.
    - *Example*: If a package with a price of $25/month and a student limit of 430 is chosen, and a school has 2 branches, the total monthly payment required is $25 x 2 = $50. Each branch receives its own 430-student limit.
    - If a school has N branches, their total subscription cost for the selected package will be: `Total Cost = N * Package Price (per billing period)`
    - Student limits are enforced **per branch** as defined in the package.
- **Subscription Handling**:
    - Upon subscription purchase, each branch is assigned its own active subscription and limit, all tracked under the school's overall subscription.
    - Renewal or payment approval will cover all active branches, each at the package price.

#### 4.1.3 Subscription Lifecycle (Duration-based)

```mermaid
graph LR
A[Subscription Purchased] --> B[Active Period - as per package duration (e.g., 30 days, 1 year, etc.)]
B --> C[Expiry Date Reached]
C --> D[Grace Period - 2 days]
D --> E{Payment Received?}
E -->|Yes| F[Renew Subscription (per selected package duration)]
E -->|No| G[Account & All Branches Suspended]
F --> B
```
- The lifecycle adapts to the chosen package duration. Renewals reinstate the selected duration for all branches covered by the package.
- If the subscription is not renewed after the grace period, access for all branches is suspended.

*Note: System logic must ensure that pricing calculations, subscription durations, and limits always reflect the selected package's configuration and number of branches under each school account.*

### 4.2 Access Control Based on Subscription Status

#### 4.2.1 Subscription Validation
```
On User Login:
1. Check this schools subscription package of subscription.end_date
2. If current_date > (end_date + grace_period):
   - Block login
   - Display payment reminder
3. If end_date < current_date <= (end_date + grace_period):
   - Allow login
   - Show warning banner
4. If current_date <= end_date:
   - Normal access
```

#### 4.2.2 Suspension Message
```
"Your subscription has expired. Please contact Telesom Sales 
Subscription ended on: [DATE]
Grace period expired on: [DATE]"
```

### 4.3 Payment Management

#### 4.3.1 Payment Approval Workflow (Practical Scenario)

- **Finance Handling**: All school payments are processed and monitored outside the system by the company's finance department.
- **Admin Role**: Once payment confirmation is received by the finance team, the system admin logs in to the main dashboard and views schools with pending subscriptions.
- **Subscription Approval**: The admin selects each pending school's subscription and approves it within the system.
    - ✅ **Approve**: Instantly activates or extends the subscription for the school and its branches for the chosen package period (e.g., 30 days, 1 year).
    - ❌ **Reject**: If required, the admin may reject the subscription with a reason; the school is then notified of the rejection (optional).
- **No School Upload Needed**: There is no functionality for schools to upload payment proof or references; all payment verification is handled outside the platform.


#### 4.3.2 Payment History Log

**Required Fields**:
- Transaction ID
- School ID
- Amount
- Payment Date
- Payment Method
- Reference Number
- Approval Status
- Approved By (User ID)
- Approval Date
- Subscription Period (From - To)
- Receipt/Invoice Number



#### 4.3.3 School Payment & Subscription Reports

Administrators can generate consolidated reports detailing all payments and subscription activity for each school. These reports support filtering by:
- **School Name/ID**
- **Date Range**
- **Subscription Status** (active, expired, grace period, suspended)
- **Payment Status** (paid, pending, rejected)

**Report Contents:**
- School Name & ID
- Package Name
- Subscription Period (Start/End Dates)
- Payment Amount
- Payment Method
- Payment Date
- Approval Status
- Approval/Rejection Date & User
- Grace Period Dates
- Outstanding Dues (if any)
- Invoice/Receipt Number

Reports must be exportable and printed in both excel and PDF formats.

-Note:- this report should be implemented as a stored procedure in the database and in the UI we provide the parameters and call the procedure.

##### Example: "School Payment & Subscription Report"
| School | Package | Subscribed From | Subscribed To | Payment Date | Amount | Status | Grace Period End | Approved By | Invoice No. |
|--------|---------|----------------|--------------|--------------|--------|--------|------------------|-------------|-------------|
| Noradin | Standard | 2024-01-01 | 2024-12-31 | 2024-01-01 | $500 | Active | 2025-01-15 | admin01 | INV-2221 |

Reports provide financial tracking and audit capabilities for both admins and finance roles.






## 5. Enhance the main Admin Dashboard Features



### 5.2 Dashboard Metrics & Analytics

The main dashboard should provide administrators with at-a-glance widgets and key metrics, including:

#### 5.2.1 Dashboard Widgets / Key Metrics
- **Total Payment Collections**: Monthly and yearly summaries of payments received, visualized with graphs.
- **Outstanding Payments**: Total outstanding dues and count of schools with pending payments.
- **School Growth**: Growth trends of registered schools, including breakdown by branches and students (with visual graphs and comparison by month/year).
- **Active Subscriptions**: Current count and trend over time.
- **Churn Rate**: Percentage of schools not renewing subscriptions.
- **MRR (Monthly Recurring Revenue)**: Calculated for the current and previous periods.
- **Average Revenue per School**
- **Package Distribution**: Breakdown of schools by subscription package.
- **Payment Success Rate**: Approved vs. all payment attempts.

Widgets should support time-based filtering (monthly/yearly/quarterly), interactive charts (line/bar/pie), and drill-down capabilities for detailed views.

#### 5.2.2 Reports
- **Payment Collection Report** (monthly/yearly): Detailed exportable report of all payments received within a selected period.
- **Outstanding Payments Report**: List of schools with unpaid dues, amounts, and payment status.
- **School Growth Report**: Historical data and visualizations showing new schools, new branches, and student growth over time.

All reports should be easily exportable (Excel/PDF) and printable. 

> **Note:** All dashboard metrics and reporting data should be sourced via optimized backend queries or stored procedures to ensure performance.

> **Note:** All reports should be stored procedures in the database. so in the dashboard we just pass them the parameters.

---



## 7. Implementation Considerations


### 7.1 Performance Optimization
- Index on subscription.end_date for faster queries



## 8. Testing Requirements (which am gonna do manually)

### 8.1 Test Scenarios
- Student limit enforcement across branches
- Branch creation during school registration
- Subscription expiry and grace period
- Payment approval workflow
- Access control during suspension

### 8.2 Edge Cases
- School with 0 students trying to add first student
- Concurrent student registrations near limit
- Payment submission during grace period
- Multiple payment attempts for same period
- Branch limit modifications after creation



**NOTE*: The main dashboard is located in Modules/MainApp