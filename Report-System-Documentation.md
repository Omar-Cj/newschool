# TelesomERP Report System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Database Schema](#database-schema)
4. [Core Components](#core-components)
5. [Report Lifecycle](#report-lifecycle)
6. [Creating New Reports](#creating-new-reports)
7. [Export System](#export-system)
8. [Permission & Security](#permission--security)
9. [Developer Namespaces](#developer-namespaces)
10. [Code Examples](#code-examples)

---

## System Overview

The TelesomERP reporting system is built on **KoolReport**, a powerful Laravel-based reporting framework. It provides a centralized, permission-controlled reporting infrastructure supporting multiple export formats and dynamic filtering.

### Key Features
- **50+ Report Types** across HR, Sales, Inventory, Finance, CRM, and Asset modules
- **Centralized Report Center** for report registry and management
- **Multi-Format Export** (PDF, Excel, Print)
- **Role-Based Permissions** for secure report access
- **Dynamic Filtering** (date ranges, sites, stores, customers, products, etc.)
- **Database Views Integration** for optimized queries
- **Company Branding** in exported reports

### Technology Stack
- **Framework**: Laravel (PHP)
- **Report Engine**: KoolReport
- **Export Libraries**:
  - PDF: KoolReport PDF Export
  - Excel: KoolReport Excel Export (BigSpreadsheet, Standard, CSV)
- **Database**: MySQL with optimized views
- **Frontend**: Blade templates with DataTables, Select2

---

## Architecture

### Component Hierarchy

```
┌─────────────────────────────────────────────────┐
│           Report Center (Registry)              │
│  - report_center table                          │
│  - report_category table                        │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│               Routes & Controllers               │
│  - ReportCenterController (main)                │
│  - Specialized Controllers (per developer)       │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│              Report Classes                      │
│  - Extend KoolReport                            │
│  - Implement data queries                        │
│  - Handle parameter binding                      │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│                 Views                            │
│  - Display view (.view.php)                     │
│  - PDF export view (Pdf.view.php)               │
│  - Excel export view (Excel.view.php)           │
└─────────────────────────────────────────────────┘
```

### Data Flow

1. **User Access** → Report Center lists available reports (filtered by role)
2. **Report Selection** → Permission check against user roles
3. **Parameter Input** → User sets filters (dates, sites, etc.)
4. **Data Query** → Report class executes database queries
5. **Data Processing** → KoolReport processes/aggregates data
6. **View Rendering** → Display view shows results with widgets
7. **Export (Optional)** → Generate PDF/Excel/Print output

---

## Database Schema

### `report_center` Table
Central registry for all system reports.

| Column | Type | Description |
|--------|------|-------------|
| `id` | integer | Primary key |
| `name` | string(2000) | Report display name |
| `module` | string(200) | Module (HR, Sales, Inventory, etc.) |
| `category` | string(200) | Foreign key to report_category.id |
| `status` | string(40) | 'active' or 'pending' |
| `url` | string(1000) | Laravel route name |
| `description` | string(2000) | Report description |
| `role` | string(2000) | Comma-separated role IDs with access |
| `export` | integer | 1 = exportable, 0 = view only |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update timestamp |
| `deleted_at` | timestamp | Soft delete timestamp |

### `report_category` Table
Categorizes reports for organization.

| Column | Type | Description |
|--------|------|-------------|
| `id` | integer | Primary key |
| `name` | string(200) | Category name |
| `module` | string(200) | Module name |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Last update timestamp |
| `deleted_at` | timestamp | Soft delete timestamp |

### Database Views
The system uses optimized database views for reporting:

- `v_Inventory_ReorderLevel_Report` - Inventory reorder alerts
- `v_Inventory_Aging_Report` - Inventory aging analysis
- `scm_v_stock_status_report` - Stock status overview
- `scm_v_stock_minimum_report` - Minimum stock alerts

---

## Core Components

### 1. Models

#### ReportCenter Model
**Location**: `app/Models/ReportCenter.php`

```php
class ReportCenter extends Model
{
    protected $table = "report_center";
    protected $fillable = [
        "name", "category", "status", "url",
        "description", "role", "module", "export"
    ];

    function ReportCategory() {
        return $this->hasOne(ReportCategory::class, "id", "category");
    }
}
```

#### ReportCategory Model
**Location**: `app/Models/ReportCategory.php`

```php
class ReportCategory extends Model
{
    use SoftDeletes;

    protected $table = "report_category";
    protected $fillable = ["name", "module"];
}
```

### 2. Report Classes

Report classes extend `\koolreport\KoolReport` and use Laravel integration traits.

**Base Structure:**
```php
namespace App\reports\[developer];

use koolreport\processes\*;

class ReportName extends \koolreport\KoolReport
{
    // Essential Traits
    use \koolreport\laravel\Friendship;      // Laravel integration
    use \koolreport\export\Exportable;       // Export capability
    use \koolreport\inputs\Bindable;         // Parameter binding
    use \koolreport\inputs\POSTBinding;      // POST data binding

    // Define default parameter values
    protected function defaultParamValues() {
        return array(
            "param1" => "default_value",
            "duration" => [start_date, end_date]
        );
    }

    // Bind parameters to inputs
    protected function bindParamsToInputs() {
        return array(
            "param1" => "input_name"
        );
    }

    // Setup data pipeline
    function setup() {
        $this->src("")  // Use default Laravel connection
            ->query("SELECT ... WHERE ... :param1")
            ->params([":param1" => $this->params["param1"]])
            ->pipe($this->dataStore("store_name"));
    }
}
```

**Key Methods:**
- `defaultParamValues()` - Sets default filter values
- `bindParamsToInputs()` - Maps parameters to form inputs
- `setup()` - Defines data queries and processing pipeline
- `src("")` - Uses Laravel's default database connection

### 3. Controllers

#### Main Report Center Controller
**Location**: `app/Http/Controllers/ReportCenterController.php`

**Key Methods:**

```php
// List all reports (filtered by role)
function index() {
    $roles = Role::all();
    return view('reportcenter.index', ["roles" => $roles]);
}

// Get report data for DataTable
function data(Request $request) {
    $report_centers = ReportCenter::with("ReportCategory")->get();

    // Filter by user role
    foreach ($report_centers as $report_center) {
        if (!in_array($request->user()->roles[0]->id,
            explode(",", $report_center->role))) {
            continue;
        }
        // Build action buttons and data array
    }
    return $data;
}

// Create/Update report in registry
function create(Request $request) {
    // Validate route exists
    if (!Route::has($data["url"])) {
        return ["success" => false, "msg" => "Route doesn't exist"];
    }

    // Create or update
    if ($request->has("id")) {
        ReportCenter::findorfail($id)->update($data);
    } else {
        ReportCenter::create($data);
    }
}

// Display specific report
function report_name(Request $request) {
    // Validate report_id parameter
    if (!$request->filled('report_id')) {
        return view('errors', ["error_code" => "422"]);
    }

    // Get report config
    $ReportCenter = ReportCenter::findorfail(
        base64_decode($request->query('report_id'))
    );

    // Permission check
    if (empty(array_intersect(
        $request->user()->roles->pluck('id')->toArray(),
        explode(",", $ReportCenter->role)
    ))) {
        return view('errors', ["error_code" => "401"]);
    }

    // Run report
    $report = new ReportClass(["report_data" => $ReportCenter]);
    $report->run();
    return view('reportcenter', ["report" => $report]);
}

// Export report
function export_report(Request $request) {
    $report = new ReportClass;
    $exportType = $request->type;

    if ($exportType === "excell") {
        $report->run()
            ->exportToExcel("excelViewName")
            ->toBrowser("filename.xlsx");
    } else if ($exportType === "pdf") {
        $report->run()
            ->export('pdfViewName')
            ->pdf(["format" => "A4", "orientation" => "portrait"])
            ->toBrowser("filename.pdf");
    } else if ($exportType === "print") {
        return view('print', ["report" => $report]);
    }
}
```

#### Specialized Controllers
Each developer has a dedicated controller namespace:

- `App\Http\Controllers\AbuyahyaReports\ReportsController`
- `App\Http\Controllers\kaydReports\ReportsController`
- `App\Http\Controllers\cxguledReports\cxReportCenterController`
- `App\Http\Controllers\abdirazakReports\AbdirazakreportsController`

### 4. Views

#### Display View (.view.php)
**Location**: `app/reports/[developer]/ReportName.view.php`

```php
<?php
use \koolreport\widgets\koolphp\Table;
use \koolreport\inputs\DateRangePicker;
?>
<html>
<head>
    <title>Report Title</title>
</head>
<body>
    <!-- Filter Modal -->
    <div class="modal" id="modal">
        <form method="post">
            <?php echo csrf_field(); ?>

            <!-- Date Range Filter -->
            <?php
            DateRangePicker::create(array(
                "name" => "duration"
            ))
            ?>

            <!-- Dropdown Filters -->
            <select name="site" class="form-control">
                <option value="%">-- All --</option>
                <?php foreach($this->dataStore("sites") as $item) { ?>
                    <option value="<?php echo $item["id"] ?>">
                        <?php echo $item["name"] ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Report Display -->
    <div class="card">
        <div class="card-header">
            <!-- Export Buttons -->
            <form method="post" action="<?php echo route('export_route') ?>">
                <button name="type" value="excell">Excel</button>
                <button name="type" value="pdf">PDF</button>
                <button name="type" value="print">Print</button>
            </form>
        </div>

        <div class="card-body">
            <?php
            Table::create([
                "dataSource" => $this->dataStore("reportdata"),
                "cssClass" => array(
                    "table" => "table table-bordered table-striped"
                ),
                "columns" => [
                    "column1" => "Display Name",
                    "column2" => array(
                        "label" => "Amount",
                        "prefix" => "$",
                        "decimals" => 2,
                        "footer" => "sum"
                    )
                ],
                "paging" => array("pageSize" => 50),
                "showFooter" => true
            ]);
            ?>
        </div>
    </div>
</body>
</html>
```

#### PDF Export View (Pdf.view.php)
**Location**: `app/reports/[developer]/ReportNamePdf.view.php`

```php
<?php
use \koolreport\widgets\koolphp\Table;
use Carbon\Carbon;
?>
<html>
<head>
    <title>Report PDF</title>
    <style>
        /* PDF-specific styles */
        body { background-color: white; }
        .page-header { text-align: left; }
    </style>
</head>
<body>
    <!-- Company Header -->
    <img src="<?php echo asset('/uploads/company/' .
        $this->dataStore('company_info')[0]['logo_report']) ?>" />

    <div style="border-bottom: 1px solid black;">
        <h5><?php echo $this->dataStore('company_info')[0]['name'] ?></h5>
        <h5><?php echo $this->dataStore('company_info')[0]['address'] ?></h5>
        <h5>Tel: <?php echo $this->dataStore('company_info')[0]['telephone1'] ?></h5>
    </div>

    <!-- Report Info -->
    <h5>Start date: <?php echo Carbon::create($this->params["duration"][0])
        ->format("d M Y") ?></h5>
    <h5>End date: <?php echo Carbon::create($this->params["duration"][1])
        ->format("d M Y") ?></h5>
    <h5>Records: <?php echo $this->dataStore("reportdata")->countData() ?></h5>

    <h1>Report Title</h1>

    <!-- Data Table -->
    <?php
    Table::create([
        "dataSource" => $this->dataStore("reportdata"),
        "cssClass" => array("table" => "table-bordered"),
        "columns" => $columns,
        "showFooter" => true
    ]);
    ?>
</body>
</html>
```

#### Excel Export View (Excel.view.php)
Similar structure to PDF but optimized for spreadsheet format.

### 5. Routes

Routes are organized by developer in separate route files.

**Pattern:**
```php
// routes/[developer].php

use App\Http\Controllers\[Developer]Reports\ReportsController;

Route::prefix("reports[developer]")->name("reports[developer]")->group(function () {

    // Display report
    route::match(["get", "post"], "/report_name",
        [ReportsController::class, "report_name"])
        ->name(".report_name");

    // Export report
    route::match(["get", "post"], "/report_name/export",
        [ReportsController::class, "export_report_name"])
        ->name(".export_report_name");
});

// Report Center Management
Route::prefix("report_center")->name("report_center")->group(function () {
    route::get("/", [ReportCenterController::class, "index"])
        ->name(".index");
    route::post("/data", [ReportCenterController::class, "data"])
        ->name(".data");
    route::post("/create", [ReportCenterController::class, "create"])
        ->name(".create");
    route::get("/getSingle/{id}", [ReportCenterController::class, "getSingle"])
        ->name(".getSingle");
    route::post("/delete", [ReportCenterController::class, "delete"])
        ->name(".delete");
});
```

---

## Report Lifecycle

### 1. Registration Phase
```sql
INSERT INTO report_center (
    name, module, category, status, url,
    description, role, export
) VALUES (
    'Sales Summary Report',
    'Sales',
    1,  -- category_id
    'active',
    'reportsabu.salessum',  -- route name
    'Summary of sales by date, site, and customer',
    '1,2,3',  -- role IDs (comma-separated)
    1  -- exportable
);
```

### 2. Access Phase
1. User navigates to Report Center
2. System queries `report_center` with role filter
3. Only reports matching user's role are displayed
4. User clicks "View" button

### 3. Execution Phase
1. **Route Resolution**: `route('reportsabu.salessum')`
2. **Permission Check**: Verify user role in report's role list
3. **Parameter Binding**: GET/POST parameters bind to report class
4. **Data Query**: Report class `setup()` executes SQL queries
5. **Data Processing**: KoolReport processes data (Group, Sort, Aggregate)
6. **Data Storage**: Results saved to dataStores

### 4. Rendering Phase
1. **View Selection**: `.view.php` for display
2. **Widget Rendering**: Tables, Charts, Forms rendered
3. **Output**: HTML displayed to user

### 5. Export Phase (Optional)
1. **Format Selection**: User chooses PDF/Excel/Print
2. **View Selection**:
   - PDF: `Pdf.view.php`
   - Excel: `Excel.view.php`
   - Print: Special print view
3. **Generation**: KoolReport export libraries process
4. **Download**: File sent to browser

---

## Creating New Reports

### Step-by-Step Guide

#### Step 1: Create Report Class
**Location**: `app/reports/[your_namespace]/ReportName.php`

```php
<?php
namespace App\reports\yournamespace;

use koolreport\processes\Group;
use koolreport\processes\Sort;
use Carbon\Carbon;

class SalesReport extends \koolreport\KoolReport
{
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\inputs\Bindable;
    use \koolreport\inputs\POSTBinding;

    protected function defaultParamValues()
    {
        return array(
            "duration" => array(
                Carbon::now()->firstOfMonth()->format("Y-m-d"),
                Carbon::now()->format("Y-m-d")
            ),
            "site" => "%"
        );
    }

    protected function bindParamsToInputs()
    {
        return array(
            "duration" => "duration",
            "site" => "site"
        );
    }

    function setup()
    {
        // Main data query
        $this->src("")
            ->query("
                SELECT
                    DATE(invoice_date) as date,
                    site.name as site_name,
                    SUM(total) as total_sales
                FROM invoices
                JOIN sites ON sites.id = invoices.site_id
                WHERE invoice_date BETWEEN :start AND :end
                AND sites.id LIKE :site
                GROUP BY DATE(invoice_date), site.id
            ")
            ->params(array(
                ":start" => $this->params["duration"][0],
                ":end" => $this->params["duration"][1],
                ":site" => $this->params["site"]
            ))
            ->pipe($this->dataStore("sales_data"));

        // Lookup data for filters
        $this->src("")
            ->query("SELECT id, name FROM sites ORDER BY name")
            ->pipe($this->dataStore("sites"));

        // Company info for exports
        $this->src("")
            ->query("SELECT * FROM company_info")
            ->pipe($this->dataStore("company_info"));
    }
}
```

#### Step 2: Create Display View
**Location**: `app/reports/[your_namespace]/SalesReport.view.php`

```php
<?php
use \koolreport\widgets\koolphp\Table;
use \koolreport\inputs\DateRangePicker;
?>
<html>
<head><title>Sales Report</title></head>
<body>
    <div class="card">
        <div class="card-header">
            <!-- Filter Button -->
            <button data-bs-toggle="modal" data-bs-target="#filterModal">
                Filters
            </button>

            <!-- Export Form -->
            <form method="post" action="<?php echo route('yourns.export_sales') ?>">
                <?php echo csrf_field(); ?>

                <!-- Hidden inputs to pass current filters -->
                <?php foreach($this->params["duration"] as $date) { ?>
                    <input type="hidden" name="duration[]" value="<?php echo $date ?>"/>
                <?php } ?>
                <input type="hidden" name="site" value="<?php echo $this->params["site"] ?>"/>

                <button name="type" value="excell">Excel</button>
                <button name="type" value="pdf">PDF</button>
                <button name="type" value="print">Print</button>
            </form>
        </div>

        <div class="card-body">
            <?php
            Table::create([
                "dataSource" => $this->dataStore("sales_data"),
                "columns" => [
                    "date" => "Date",
                    "site_name" => "Site",
                    "total_sales" => array(
                        "label" => "Total Sales",
                        "prefix" => "$",
                        "decimals" => 2,
                        "footer" => "sum",
                        "footerText" => "<b>Total:</b> @value"
                    )
                ],
                "cssClass" => ["table" => "table table-bordered"],
                "paging" => ["pageSize" => 50],
                "showFooter" => true
            ]);
            ?>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal" id="filterModal">
        <form method="post">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label>Date Range</label>
                <?php DateRangePicker::create(["name" => "duration"]) ?>
            </div>

            <div class="form-group">
                <label>Site</label>
                <select name="site" class="form-control">
                    <option value="%">-- All Sites --</option>
                    <?php foreach($this->dataStore("sites") as $site) { ?>
                        <option value="<?php echo $site["id"] ?>"
                            <?php echo $this->params["site"] == $site["id"] ? "selected" : "" ?>>
                            <?php echo $site["name"] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit">Apply Filters</button>
        </form>
    </div>
</body>
</html>
```

#### Step 3: Create Export Views

**PDF View** - `app/reports/[your_namespace]/SalesReportPdf.view.php`
```php
<?php
use \koolreport\widgets\koolphp\Table;
use Carbon\Carbon;
?>
<html>
<head>
    <style>
        body { background-color: white; margin: 50px; }
        .header { border-bottom: 1px solid black; margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Company Header -->
    <img src="<?php echo asset('/uploads/company/' .
        $this->dataStore('company_info')[0]['logo_report']) ?>"
        width="150px" style="display: block; margin: 0 auto;"/>

    <div class="header">
        <h5><?php echo $this->dataStore('company_info')[0]['name'] ?></h5>
        <p>Period: <?php echo Carbon::create($this->params["duration"][0])->format("d M Y") ?>
           - <?php echo Carbon::create($this->params["duration"][1])->format("d M Y") ?></p>
        <p>Records: <?php echo $this->dataStore("sales_data")->countData() ?></p>
    </div>

    <h1>Sales Report</h1>

    <?php
    Table::create([
        "dataSource" => $this->dataStore("sales_data"),
        "columns" => [
            "date" => "Date",
            "site_name" => "Site",
            "total_sales" => array(
                "label" => "Total Sales",
                "prefix" => "$",
                "decimals" => 2,
                "footer" => "sum"
            )
        ],
        "showFooter" => true
    ]);
    ?>
</body>
</html>
```

**Excel View** - `app/reports/[your_namespace]/SalesReportExcel.view.php`
(Similar structure, optimized for spreadsheet)

#### Step 4: Add Controller Methods
**Location**: `app/Http/Controllers/YourNamespaceReports/ReportsController.php`

```php
public function sales_report(Request $request)
{
    if (!$request->filled('report_id')) {
        return view('errors', [
            "error_code" => "422",
            "msg" => "Report ID required"
        ]);
    }

    $ReportCenter = ReportCenter::findorfail(
        base64_decode($request->query('report_id'))
    );

    // Permission check
    if (empty(array_intersect(
        $request->user()->roles->pluck('id')->toArray(),
        explode(",", $ReportCenter->role)
    ))) {
        return view('errors', [
            "error_code" => "401",
            "msg" => "Access denied"
        ]);
    }

    $report = new SalesReport(["report_data" => $ReportCenter]);
    $report->run();

    return view('reportcenter', [
        "report" => $report,
        "title" => "Sales Report"
    ]);
}

public function export_sales_report(Request $request)
{
    $report = new SalesReport;
    $exportType = $request->type;

    if ($exportType === "excell") {
        $report->run()
            ->exportToExcel("SalesReportExcel")
            ->toBrowser("sales_report_" . now() . ".xlsx");
    }
    else if ($exportType === "pdf") {
        $report->run()
            ->export('SalesReportPdf')
            ->pdf([
                "format" => "A4",
                "orientation" => "landscape"
            ])
            ->toBrowser("sales_report_" . now() . ".pdf");
    }
    else if ($exportType === "print") {
        $report->run();
        return view('print', [
            "report" => $report,
            "reportfilename" => "SalesReportPdf"
        ]);
    }
}
```

#### Step 5: Register Routes
**Location**: `routes/[your_namespace].php`

```php
Route::prefix("reportsyourns")->name("yourns")->group(function () {
    route::match(["get", "post"], "/sales",
        [ReportsController::class, "sales_report"])
        ->name(".sales");

    route::match(["get", "post"], "/sales/export",
        [ReportsController::class, "export_sales_report"])
        ->name(".export_sales");
});
```

#### Step 6: Register in Report Center
```sql
INSERT INTO report_center (
    name,
    module,
    category,
    status,
    url,
    description,
    role,
    export,
    created_at,
    updated_at
) VALUES (
    'Sales Report',
    'Sales',
    1,
    'active',
    'yourns.sales',
    'Daily sales summary by site',
    '1,2,3',  -- Admin, Manager, Sales roles
    1,
    NOW(),
    NOW()
);
```

---

## Export System

### Export Formats

#### 1. Excel Export
**Traits Required:**
```php
use \koolreport\excel\ExcelExportable;
use \koolreport\excel\BigSpreadsheetExportable;  // For large datasets
use \koolreport\excel\CSVExportable;
```

**Controller Method:**
```php
$report->run()
    ->exportToExcel("ExcelViewName")
    ->toBrowser("filename.xlsx");
```

**Features:**
- Multiple sheets support
- Cell formatting
- Formulas and calculations
- Large dataset handling (BigSpreadsheet for 1M+ rows)
- CSV alternative for simple exports

#### 2. PDF Export
**Traits Required:**
```php
use \koolreport\export\Exportable;
```

**Controller Method:**
```php
$report->run()
    ->export('PdfViewName')
    ->pdf([
        "format" => "A4",           // A4, Letter, Legal
        "orientation" => "portrait"  // portrait, landscape
    ])
    ->toBrowser("filename.pdf");
```

**Features:**
- Custom page sizes
- Header/footer support
- Company branding
- Page numbering
- Landscape/portrait modes

#### 3. Print Export
**Controller Method:**
```php
$report->run();
return view('print', [
    "report" => $report,
    "reportfilename" => "PdfViewName"
]);
```

**Features:**
- Browser-optimized layout
- Print-friendly CSS
- Direct browser print dialog

### Export View Requirements

**Common Elements:**
1. **Company Info**: Logo, name, address, contact
2. **Report Period**: Start/end dates
3. **Record Count**: Total records in report
4. **Data Table**: Formatted results
5. **Footer Totals**: Sum, average, count aggregations
6. **Timestamp**: Report generation time

---

## Permission & Security

### Role-Based Access

#### Permission Check Flow
```php
// 1. Retrieve report configuration
$ReportCenter = ReportCenter::findorfail($report_id);

// 2. Get user's roles
$userRoleIds = $request->user()->roles->pluck('id')->toArray();

// 3. Get report's allowed roles
$reportRoleIds = explode(",", $ReportCenter->role);

// 4. Check intersection
if (empty(array_intersect($userRoleIds, $reportRoleIds))) {
    return view('errors', [
        "error_code" => "401",
        "msg" => "You don't have permission to access this report"
    ]);
}
```

### Security Best Practices

1. **Always validate report_id parameter**
```php
if (!$request->filled('report_id')) {
    return view('errors', ["error_code" => "422"]);
}
```

2. **Base64 encode report IDs in URLs**
```php
$encoded_id = base64_encode($report_id);
$url = route('report.view') . "?report_id=" . $encoded_id;
```

3. **Use parameterized queries**
```php
->query("SELECT * FROM table WHERE id = :id")
->params([":id" => $value])
```

4. **Validate route existence**
```php
if (!Route::has($data["url"])) {
    return ["success" => false, "msg" => "Route doesn't exist"];
}
```

5. **Check permissions in both controller and view**
```php
// Controller
if (!auth()->user()->can("report-view")) { ... }

// View
@can('report-export')
    <button>Export</button>
@endcan
```

---

## Developer Namespaces

The system organizes reports by developer to maintain code ownership and modularity.

### Active Namespaces

| Namespace | Location | Controller | Route Prefix |
|-----------|----------|------------|--------------|
| **ibrahim** | `app/reports/ibrahim/` | `ReportCenterController` | `report_center.*` |
| **abuyahya** | `app/reports/abuyahya/` | `AbuyahyaReports\ReportsController` | `reportsabu.*` |
| **kayd** | `app/reports/kayd/` | `kaydReports\ReportsController` | `reportskayd.*` |
| **muktar** | `app/reports/muktar/` | `GeneralReportController` | Various |
| **cguled** | `app/reports/cguled/` | `cxguledReports\cxReportCenterController` | `reportscx.*` |
| **abdirazak** | `app/reports/abdirazak/` | `abdirazakReports\AbdirazakreportsController` | `reportsabdi.*` |
| **bosir** | `app/reports/bosir/` | `ReportCenterController` | `reportbosir.*` |

### Namespace Structure
```
app/reports/[developer]/
├── ReportClass.php              # Report logic
├── ReportClass.view.php         # Display view
├── ReportClassPdf.view.php      # PDF export
├── ReportClassExcel.view.php    # Excel export
└── ...
```

---

## Code Examples

### Example 1: Sales Summary Report (Complete)

#### Report Class
**File**: `app/reports/abuyahya/SalesSummaryReport.php`

```php
<?php
namespace App\reports\abuyahya;

use koolreport\processes\Group;
use koolreport\processes\OnlyColumn;
use Carbon\Carbon;

class SalesSummaryReport extends \koolreport\KoolReport
{
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;
    use \koolreport\inputs\Bindable;
    use \koolreport\inputs\POSTBinding;

    protected function defaultParamValues()
    {
        return array(
            "columns" => array(
                "DATE_FORMAT(a.invoice_date, '%d %M, %Y') as `Invoice Date`",
                "d.name as Site",
                "g.name as Customer",
                "b.name as `Invoice Type`"
            ),
            "site" => "%",
            "store" => "%",
            "invtype" => "%",
            "custtype" => "%",
            "duration" => array(
                Carbon::now()->firstOfMonth()->format("Y-m-d"),
                Carbon::now()->format("Y-m-d")
            )
        );
    }

    protected function bindParamsToInputs()
    {
        return array(
            "columns" => "columns",
            "site" => "site",
            "store" => "store",
            "invtype" => "invtype",
            "custtype" => "custtype",
            "duration" => "duration"
        );
    }

    function setup()
    {
        $this->src("")
            ->query("
                SELECT
                    ROW_NUMBER() OVER(ORDER BY a.invoice_date ASC) as Sqn,
                    " . implode(",", $this->params["columns"]) . ",
                    total as Total,
                    (total - (SUM(receipt.amount) + SUM(receipt.discount) +
                     SUM(receipt.promotion))) as Balance
                FROM invoices a
                JOIN invoice_types b ON a.invoice_type = b.id
                JOIN inv_status c ON c.id = a.inv_status
                JOIN sites d ON d.id = a.site_id
                JOIN stores e ON e.id = a.store_id
                JOIN users f ON f.id = a.createdby
                JOIN customers g ON g.id = a.cust_id
                JOIN customer_type h ON h.id = g.customertype_id
                JOIN receipt ON receipt.invoice_id = a.id
                WHERE a.invoice_date BETWEEN :start AND :end
                AND d.id LIKE :site
                AND e.id LIKE :store
                AND b.id LIKE :invtype
                AND h.id LIKE :custtype
                GROUP BY a.id
            ")
            ->params(array(
                ":start" => $this->params["duration"][0],
                ":end" => $this->params["duration"][1],
                ":site" => $this->params["site"],
                ":store" => $this->params["store"],
                ":invtype" => $this->params["invtype"],
                ":custtype" => $this->params["custtype"]
            ))
            ->pipe($this->dataStore("reportdata"));

        // Lookup data for filters
        $this->src("")
            ->query("SELECT * FROM company_info")
            ->pipe($this->dataStore("company_info"));

        $this->src("")
            ->query("SELECT id, name FROM sites ORDER BY name ASC")
            ->pipe($this->dataStore("sites"));

        $this->src("")
            ->query("SELECT id, name FROM stores ORDER BY name ASC")
            ->pipe($this->dataStore("stores"));

        $this->src("")
            ->query("SELECT id, name FROM customer_type ORDER BY name ASC")
            ->pipe($this->dataStore("customertypes"));

        $this->src("")
            ->query("SELECT id, name FROM invoice_types ORDER BY name ASC")
            ->pipe($this->dataStore("invoicetypes"));
    }
}
```

#### Controller Methods
**File**: `app/Http/Controllers/AbuyahyaReports/ReportsController.php`

```php
public function salessummary(Request $request)
{
    if (!$request->filled('report_id')) {
        return view('errors', [
            "error_code" => "422",
            "msg" => "Report ID required"
        ]);
    }

    $ReportCenter = ReportCenter::findorfail(
        base64_decode($request->query('report_id'))
    );

    if (empty(array_intersect(
        $request->user()->roles->pluck('id')->toArray(),
        explode(",", $ReportCenter->role)
    ))) {
        return view('errors', [
            "error_code" => "401",
            "msg" => "Access denied"
        ]);
    }

    $report = new SalesSummaryReport(["report_data" => $ReportCenter]);
    $report->run();

    return view('reportcenter', [
        "report" => $report,
        "title" => "Sales Summary Report"
    ]);
}

public function export_salessummary(Request $request)
{
    $report = new SalesSummaryReport;
    $exportType = $request->type;

    if ($exportType === "excell") {
        $report->run()
            ->exportToExcel("SalesSummaryReportExcel")
            ->toBrowser("sales_summary_" . now() . ".xlsx");
    }
    else if ($exportType === "pdf") {
        $report->run()
            ->export('SalesSummaryReportPdf')
            ->pdf([
                "format" => "A4",
                "orientation" => "portrait"
            ])
            ->toBrowser("sales_summary_" . now() . ".pdf");
    }
    else if ($exportType === "print") {
        $report->run();
        return view('print', [
            "report" => $report,
            "reportfilename" => "SalesSummaryReportPdf"
        ]);
    }
}
```

#### Routes
**File**: `routes/abuyahya.php`

```php
Route::prefix("reportsabu")->name("reportsabu")->group(function () {
    route::match(["get", "post"], "/salessum",
        [ReportsController::class, "salessummary"])
        ->name(".salessum");

    route::match(["get", "post"], "/salessum/export",
        [ReportsController::class, "export_salessummary"])
        ->name(".export_salessum");
});
```

### Example 2: Report with Data Aggregation

```php
function setup()
{
    $node = $this->src("")
        ->query("SELECT * FROM employees");

    // Group by blood type
    $node->pipe(new Group(array(
        "by" => "blood_type",
        "count" => "frequency"
    )))->saveTo($bloodtype);

    // Group by nationality
    $node->pipe(new Group(array(
        "by" => "nationality",
        "count" => "frequency"
    )))->saveTo($nationality);

    // Store original data
    $node->pipe($this->dataStore("employees"));

    // Store aggregated data
    $bloodtype->pipe(new OnlyColumn(array('blood_type', 'frequency')))
        ->pipe($this->dataStore('employee_bloodtype'));

    $nationality->pipe(new OnlyColumn(array('nationality', 'frequency')))
        ->pipe($this->dataStore('employee_nationality'));
}
```

### Example 3: Dynamic Column Selection

```php
// In view
<select name="columns[]" multiple>
    <option value="a.date as Date">Date</option>
    <option value="b.name as Customer">Customer</option>
    <option value="c.amount as Amount">Amount</option>
</select>

// In report class
function setup()
{
    $columns = implode(",", $this->params["columns"]);

    $this->src("")
        ->query("SELECT {$columns} FROM table WHERE ...")
        ->pipe($this->dataStore("data"));
}

// In display view
<?php
$tableColumns = [];
foreach($this->params["columns"] as $col) {
    if (str_contains($col, " as ")) {
        $name = trim(str_replace("`", "", explode("as", $col)[1]));
        $tableColumns[$name] = $name;
    }
}

Table::create([
    "dataSource" => $this->dataStore("data"),
    "columns" => $tableColumns
]);
?>
```

---

## Advanced Features

### 1. Custom Helper Functions

Many reports use a custom `Helper` trait for currency formatting:

```php
use App\Trait\Helper;

class ReportClass extends \koolreport\KoolReport
{
    use Helper;

    // In view
    "prefix" => $this->get_currency_symbol() . " "
}
```

### 2. Database Views Integration

Reports can leverage database views for optimized queries:

```php
$this->src("")
    ->query("SELECT * FROM v_Inventory_ReorderLevel_Report WHERE ...")
    ->pipe($this->dataStore("reorder_alerts"));
```

### 3. Multi-Sheet Excel Reports

```php
$report->run()
    ->exportToExcel("Sheet1ViewName")
    ->exportToExcel("Sheet2ViewName")
    ->toBrowser("multi_sheet_report.xlsx");
```

### 4. Conditional Formatting

```php
Table::create([
    "columns" => [
        "status" => array(
            "label" => "Status",
            "formatValue" => function($value) {
                if ($value == "Pending") {
                    return "<span class='badge bg-warning'>{$value}</span>";
                } else {
                    return "<span class='badge bg-success'>{$value}</span>";
                }
            }
        )
    ]
]);
```

### 5. Chart Integration

```php
use koolreport\widgets\google\PieChart;

PieChart::create(array(
    "title" => "Sales by Category",
    "dataSource" => $this->dataStore('sales_by_category'),
    "columns" => array("category", "amount"),
    "options" => array(
        "width" => 900,
        "height" => 500
    )
));
```

---

## Troubleshooting

### Common Issues

#### 1. Report Not Showing in Report Center
**Cause**: User role not in report's role list
**Solution**: Update `role` column in `report_center` table

#### 2. Export Failing
**Cause**: Missing export view or incorrect view name
**Solution**: Ensure view file exists and matches export method name

#### 3. Permission Denied Error
**Cause**: Role mismatch or missing permission
**Solution**: Check user roles and report configuration

#### 4. Data Not Displaying
**Cause**: Incorrect dataStore name or empty result set
**Solution**: Verify dataStore names match between setup() and view

#### 5. Filter Not Working
**Cause**: Parameter binding mismatch
**Solution**: Ensure bindParamsToInputs() matches form input names

### Debug Tips

1. **Check Data Stores**
```php
// In view
<?php print_r($this->dataStore("store_name")); ?>
```

2. **Verify Parameters**
```php
// In view
<?php var_dump($this->params); ?>
```

3. **Test Queries**
```php
// Run query directly in database to verify results
```

4. **Check Route**
```php
// Verify route exists
Route::has('route.name');
```

---

## Best Practices

### 1. Code Organization
- Group reports by module/developer
- Use consistent naming conventions
- Keep related views together

### 2. Performance
- Use database views for complex queries
- Implement pagination for large datasets
- Cache lookup data (sites, categories, etc.)
- Use indexes on filtered columns

### 3. Security
- Always validate input parameters
- Use parameterized queries
- Implement role-based access
- Sanitize user inputs

### 4. Maintainability
- Document complex queries
- Use meaningful variable names
- Follow existing patterns
- Keep views DRY (Don't Repeat Yourself)

### 5. User Experience
- Provide clear filter options
- Show record counts
- Include export options
- Display loading indicators

---

## Appendix

### A. Report Types by Module

#### Human Resources (HR)
- Employee List Report
- Payroll Sheet
- Salary Summary (by site)
- Leave Balance
- Leave Encashment
- Monthly Leaves Report
- Severance Balance
- Severance Payment Report
- Overtime Report
- Overtime Summary
- Fine Report
- Allowance Report
- Skills Report
- Advance Report
- Daily/Monthly Attendance
- Termination Report
- Internship Report

#### Sales & CRM
- Sales Summary Report
- Sales by Customer
- Sales by Product
- Receipt Report (Cashier & General)
- Collection Report
- Pending Invoice Report
- Lead Report
- Lead Status Report
- Lead Aging Report
- Opportunity Report
- Customer Feedback
- Customer Lifetime Value
- Customer Segmentation
- New Customer Report
- Organization by Service

#### Inventory & Supply Chain
- Inventory Balance Report
- Stock Status Report
- Stock Movement Report
- Stock Minimum Report
- Inventory Aging Report
- Inventory Reorder Level Report
- Purchase Order Report
- Supplier Balance Report
- Supplier Statement

#### Finance & Accounting
- Income Statement
- Balance Sheet
- Trail Balance
- Receivable Report
- Receivable Aging
- Payment Details Report
- Cash Receipt Report
- Expense List Report
- Customer Promotion Report
- Profit by Product
- Profit by Customer

#### Assets
- Asset List Report
- Maintenance List Report
- Depreciation Report
- Audit Trail Report
- Disposal Report
- Utilization Report
- Lifecycle Report

#### Other
- Ticket Status Report
- Event List Report
- Mobile Orders Report

### B. KoolReport Widget Reference

#### Table Widget
```php
Table::create([
    "dataSource" => $dataStore,
    "columns" => [...],
    "cssClass" => ["table" => "class-names"],
    "paging" => ["pageSize" => 50],
    "showFooter" => true,
    "removable" => true  // Allow column hiding
]);
```

#### Chart Widgets
- PieChart
- BarChart
- LineChart
- ColumnChart
- AreaChart

#### Input Widgets
- DateRangePicker
- Select2
- Checkbox
- Radio
- TextBox

### C. Useful Database Queries

#### Get all reports for a role
```sql
SELECT * FROM report_center
WHERE FIND_IN_SET('1', role) > 0
AND status = 'active';
```

#### Count reports by module
```sql
SELECT module, COUNT(*) as total
FROM report_center
GROUP BY module;
```

#### Find reports without categories
```sql
SELECT * FROM report_center
WHERE category IS NULL OR category = '';
```

---

## Support & Resources

### Documentation
- **KoolReport Docs**: https://www.koolreport.com/docs
- **Laravel Docs**: https://laravel.com/docs

### Key Files to Reference
- `app/reports/Myreport.php` - Basic report template
- `app/reports/abuyahya/SalesSummaryReport.php` - Advanced example
- `app/Http/Controllers/ReportCenterController.php` - Main controller
- `resources/views/reportcenter/reportcenter.blade.php` - Report Center UI

### Common Routes
- Report Center: `/report_center`
- Report List API: `/report_center/data` (POST)
- Create/Update Report: `/report_center/create` (POST)
- View Report: `/reports/[namespace]/[report_name]?report_id=[base64_id]`
- Export Report: `/reports/[namespace]/[report_name]/export` (POST)

---

**Document Version**: 1.0
**Last Updated**: 2025-10-12
**Maintained By**: Development Team
