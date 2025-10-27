# Branch System Documentation
## Laravel School Management System - MultiBranch Module

### Table of Contents
1. [System Overview](#system-overview)
2. [Technical Architecture](#technical-architecture)
3. [Database Structure](#database-structure)
4. [Core Features](#core-features)
5. [User Interface](#user-interface)
6. [Implementation Details](#implementation-details)
7. [Usage Guidelines](#usage-guidelines)
8. [File Locations](#file-locations)

---

## System Overview

The **MultiBranch** system in this Laravel school management application enables multi-location school management. "Branches" refer to **physical school locations or campuses**, not Git code branches.

### Purpose
- Manage multiple school locations from a single application
- Provide data isolation between different branches
- Allow users to switch between branches they have access to
- Maintain separate administrative control per branch

### Architecture
- **Modular Design**: Implemented as a separate Laravel module (`Modules/MultiBranch`)
- **Global Scope Integration**: Automatic data filtering across the entire application
- **Database-Wide Implementation**: Every table includes branch identification
- **User-Centric**: Branch association through user accounts

---

## Technical Architecture

### Core Components

#### 1. Branch Entity (`Modules/MultiBranch/Entities/Branch.php`)
```php
class Branch extends Model
{
    // Basic Laravel model for branch management
    // Extends standard Eloquent functionality
}
```

#### 2. Database Schema (`branches` table)
- **id**: Primary key (auto-increment)
- **name**: Branch/school name (required)
- **phone**: Contact phone number (optional)
- **email**: Branch email address (optional) 
- **address**: Physical address (optional)
- **lat**: Latitude for geographic location (optional)
- **long**: Longitude for geographic location (optional)
- **status**: Active/Inactive status (default: Active)
- **country_id**: Foreign key to countries table
- **timestamps**: Created and updated timestamps

#### 3. Global Branch Integration (`app/Models/BaseModel.php`)
The system implements automatic branch filtering through Laravel's global scopes:

```php
// Automatic filtering by branch_id
static::addGlobalScope('branch_id', function (Builder $builder) {
    $table = $builder->getQuery()->from;
    $branchId = auth()->user()->branch_id ?? null;
    
    if ($branchId && Schema::hasColumn($table, 'branch_id')) {
        $builder->where("{$table}.branch_id", $branchId);
    }
});

// Automatic branch_id assignment on record creation
static::creating(function ($model) {
    $branchId = auth()->user()->branch_id ?? null;
    
    if ($branchId && Schema::hasColumn($model->getTable(), 'branch_id')) {
        $model->branch_id = $branchId;
    }
});
```

---

## Database Structure

### Branch Table Schema
```sql
CREATE TABLE branches (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    address VARCHAR(255) NULL,
    lat VARCHAR(255) NULL,
    long VARCHAR(255) NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    country_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Global Branch Integration
**Critical Implementation**: A migration (`2025_01_01_122002_add_branch_id_to_all_tables.php`) adds `branch_id` column to **ALL existing database tables**:

```php
// Automatically adds branch_id to every table
foreach ($tables as $tableName) {
    Schema::table($tableName, function (Blueprint $blueprint) use ($tableName) {
        if (!Schema::hasColumn($tableName, 'branch_id')) {
            $blueprint->unsignedBigInteger('branch_id')->default(1);
        }
    });
}
```

### User-Branch Relationship
The `users` table includes:
- **branch_id**: Links users to specific branches (default: 1)
- Users can only access data from their assigned branch
- Branch switching updates the user's branch_id

---

## Core Features

### 1. Branch Management (CRUD Operations)

#### Create New Branch
- **Route**: `/branches/create`
- **Required Fields**: name, phone, email, address
- **Optional Fields**: lat, long, country_id
- **Automatic Admin Creation**: Creates admin user for each new branch

#### View All Branches
- **Route**: `/branches`
- **Features**: Paginated list with search functionality
- **Display**: Name, email, phone, address, status
- **Permissions**: Requires appropriate user permissions

#### Edit Branch
- **Route**: `/branches/{id}/edit`
- **Functionality**: Update branch information
- **Restrictions**: Based on user permissions

#### Delete Branch
- **Route**: `/branches/delete/{id}`
- **Method**: DELETE request with JSON response
- **Safety**: Soft delete with confirmation

### 2. Branch Switching
- **Route**: `/switch-branch`
- **Functionality**: Allows users to change their active branch
- **Process**: Updates user's branch_id in database
- **Effect**: All subsequent queries filter to new branch

### 3. Automatic Data Isolation
- **Global Scopes**: All queries automatically filtered by user's branch
- **Data Creation**: New records automatically assigned to user's branch
- **Security**: Users cannot access other branches' data

### 4. Geographic Location Support
- **Coordinates**: Stores latitude and longitude for each branch
- **Usage**: Enables location-based features and mapping
- **Integration**: Ready for GPS-based functionality

---

## User Interface

### Branch Management Interface

#### Navigation Path
1. **Admin Dashboard** → **Settings/Management** → **Branches**
2. Direct URL: `/branches`

#### Branch List View (`multibranch::branch.index`)
- **Layout**: Table format with pagination
- **Columns**: Serial Number, Name, Email, Phone, Address, Status, Actions
- **Actions**: Edit, Delete (based on permissions)
- **Add Button**: "Add New Branch" (permission-based visibility)

#### Branch Creation Form (`multibranch::branch.create`)
**Required Fields:**
- Branch Name
- Phone Number  
- Email Address
- Physical Address

**Optional Fields:**
- Latitude coordinates
- Longitude coordinates
- Country selection

**Additional Features:**
- Admin user creation form (embedded)
- Validation with real-time feedback
- Geographic coordinate picker (if implemented)

#### Branch Editing Form (`multibranch::branch.edit`)
- Pre-populated with existing branch data
- Same field structure as creation form
- Update confirmation and success messaging

### Branch Switching Interface
- **Location**: Typically in header or navigation
- **Method**: Dropdown or modal selection
- **Visual Feedback**: Current branch display
- **Instant Effect**: Page refresh with new branch context

---

## Implementation Details

### Repository Pattern (`Modules/MultiBranch/Repositories/BranchRepository.php`)

#### Key Methods:
```php
public function store($request)
{
    DB::transaction(function () use ($request) {
        // Create branch
        $branch = new $this->model;
        $branch->name = $request->name;
        $branch->phone = $request->phone;
        $branch->email = $request->email;
        $branch->address = $request->address;
        $branch->lat = $request->lat;
        $branch->long = $request->long;
        $branch->country_id = 1;
        $branch->save();

        // Create admin user for branch
        $user = new $this->userModel;
        $user->name = $request->user['name'];
        $user->email = $request->user['email'];
        $user->role_id = RoleEnum::ADMIN;
        $user->branch_id = $branch->id;
        $user->password = Hash::make($request->user['password']);
        $user->save();
    });
}
```

### Request Validation (`Modules/MultiBranch/Http/Requests/BranchStoreRequest.php`)
```php
public function rules(): array
{
    return [
        'name' => 'required',
        'phone' => 'required', 
        'email' => 'required',
        'address' => 'required',
    ];
}
```

### Route Definitions (`Modules/MultiBranch/Routes/web.php`)
```php
Route::prefix('branches')
    ->as('branch.')
    ->controller(BranchController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', 'edit')->name('edit');
        Route::put('{id}/update', 'update')->name('update');
        Route::delete('delete/{id}', 'destroy')->name('destroy');
    });

Route::get('switch-branch', [MultiBranchController::class, 'switchBranch'])->name('switch-branch');
```

---

## Usage Guidelines

### For System Administrators

#### Adding a New Branch
1. **Navigate** to Branches management (`/branches`)
2. **Click** "Add New Branch" button
3. **Fill** required information:
   - Branch name (e.g., "Downtown Campus", "North Branch")
   - Contact phone number
   - Email address
   - Physical address
4. **Optional**: Add geographic coordinates for location features
5. **Create Admin**: Fill admin user details for the new branch
6. **Submit** - System creates branch and admin user in single transaction

#### Managing Existing Branches
- **View**: Access branch list to see all locations
- **Edit**: Update branch information as needed
- **Status**: Activate/deactivate branches
- **Delete**: Remove unused branches (with caution)

### For Branch Users

#### Switching Between Branches
1. **Locate** branch switcher (usually in navigation/header)
2. **Select** desired branch from dropdown
3. **Confirm** switch - page will refresh with new branch data
4. **Verify** current branch indicator shows correct location

#### Understanding Data Isolation
- **Current Branch Only**: You only see data from your active branch
- **No Cross-Branch Access**: Cannot view other branches' data
- **Automatic Assignment**: New data automatically tagged to your branch
- **Branch Context**: All reports and operations are branch-specific

---

## File Locations

### Module Structure
```
Modules/MultiBranch/
├── Database/
│   ├── Migrations/
│   │   └── 2024_12_31_160536_create_branches_table.php
│   └── Seeders/
│       └── MultiBranchDatabaseSeeder.php
├── Entities/
│   └── Branch.php
├── Http/
│   ├── Controllers/
│   │   ├── BranchController.php
│   │   └── MultiBranchController.php
│   └── Requests/
│       └── BranchStoreRequest.php
├── Interfaces/
│   └── BranchInterface.php
├── Providers/
│   ├── MultiBranchServiceProvider.php
│   └── RouteServiceProvider.php
├── Repositories/
│   └── BranchRepository.php
├── Resources/views/
│   └── branch/
│       ├── index.blade.php    # Branch list view
│       ├── create.blade.php   # Branch creation form
│       └── edit.blade.php     # Branch editing form
└── Routes/
    ├── web.php               # Web routes
    └── api.php               # API routes
```

### Core Integration Files
```
app/Models/
└── BaseModel.php             # Global branch scoping

database/migrations/tenant/
├── 2014_10_12_000000_create_users_table.php  # Users with branch_id
└── 2025_01_01_122002_add_branch_id_to_all_tables.php  # Global branch integration
```

### Configuration Files
```
modules_statuses.json         # Module activation status
config/modules.php            # Module configuration
```

---

## Default Data

The system creates a default "Head Office" branch during installation:
- **Name**: Head Office
- **Phone**: 1234567890
- **Email**: headoffice@example.com
- **Address**: 123 Main St, City, Country
- **Location**: Dhaka, Bangladesh coordinates (23.8103, 90.4125)
- **Status**: Active
- **Branch ID**: 1 (default for all users)

---

This documentation provides comprehensive understanding of the branch system's architecture, functionality, and usage. The MultiBranch module enables sophisticated multi-location school management with complete data isolation and user-friendly branch switching capabilities.