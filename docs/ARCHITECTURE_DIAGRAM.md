# Dynamic Report Form System - Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                           │
│                    (Bootstrap 5 + Blade)                        │
│                                                                 │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────┐        │
│  │  Category   │  │    Report    │  │  Dynamic Form │        │
│  │    Tabs     │  │   Selector   │  │   Container   │        │
│  └─────────────┘  └──────────────┘  └───────────────┘        │
│                                                                 │
│  ┌──────────────────────────────────────────────────┐         │
│  │         Report Results & Export Buttons          │         │
│  └──────────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND COMPONENTS                          │
│                  (JavaScript ES6 Modules)                       │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │         DynamicReportForm.js (Main Component)            │  │
│  │  • Form rendering engine                                 │  │
│  │  • Parameter type handlers                               │  │
│  │  • Report execution logic                                │  │
│  │  • Results display                                       │  │
│  │  • Export functionality                                  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              │                                  │
│              ┌───────────────┼───────────────┐                 │
│              ▼               ▼               ▼                 │
│  ┌─────────────────┐ ┌──────────────┐ ┌─────────────┐        │
│  │ DependencyHandler│ │ ReportApiService│ │FormValidation│        │
│  │  • Cascading    │ │  • API calls  │ │ • Validation │        │
│  │    dropdowns    │ │  • CSRF token │ │   rules      │        │
│  │  • Multi-level  │ │  • Error      │ │ • Error msgs │        │
│  │    dependencies │ │    handling   │ │ • Real-time  │        │
│  └─────────────────┘ └──────────────┘ └─────────────┘        │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        API LAYER                                │
│                   (Laravel JSON API)                            │
│                                                                 │
│  ┌────────────────────────────────────────────────────────┐    │
│  │  GET  /api/reports                                     │    │
│  │       → List all reports grouped by category           │    │
│  ├────────────────────────────────────────────────────────┤    │
│  │  GET  /api/reports/{id}/parameters                     │    │
│  │       → Fetch parameters for selected report           │    │
│  ├────────────────────────────────────────────────────────┤    │
│  │  GET  /api/reports/parameters/{id}/dependent-values    │    │
│  │       → Get cascading dropdown values                  │    │
│  ├────────────────────────────────────────────────────────┤    │
│  │  POST /api/reports/{id}/execute                        │    │
│  │       → Execute report with parameters                 │    │
│  ├────────────────────────────────────────────────────────┤    │
│  │  POST /api/reports/{id}/export/{format}                │    │
│  │       → Export report (Excel/PDF/CSV)                  │    │
│  └────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    BACKEND SERVICES                             │
│                    (Laravel Controllers)                        │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              ReportApiController.php                     │  │
│  │  • index() - List reports                               │  │
│  │  • parameters() - Get parameters                        │  │
│  │  • dependentValues() - Get cascading values             │  │
│  │  • execute() - Run report                               │  │
│  │  • export() - Export to file                            │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    DATA LAYER                                   │
│                  (Laravel Eloquent)                             │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │   Report     │  │   Report     │  │   Report     │         │
│  │  Category    │  │     Model    │  │  Parameter   │         │
│  │              │  │              │  │              │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│         │                  │                  │                │
│         └──────────────────┼──────────────────┘                │
│                            ▼                                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATABASE                                 │
│                      (MySQL/PostgreSQL)                         │
│                                                                 │
│  ┌──────────────────┐  ┌──────────────────┐                   │
│  │ report_categories│  │     reports      │                   │
│  │  • id            │  │  • id            │                   │
│  │  • name          │  │  • category_id   │                   │
│  │  • description   │  │  • name          │                   │
│  └──────────────────┘  │  • stored_proc   │                   │
│                        └──────────────────┘                   │
│                                                                 │
│  ┌──────────────────┐  ┌──────────────────┐                   │
│  │report_parameters │  │  report_columns  │                   │
│  │  • id            │  │  • id            │                   │
│  │  • report_id     │  │  • report_id     │                   │
│  │  • name          │  │  • name          │                   │
│  │  • type          │  │  • label         │                   │
│  │  • depends_on    │  │  • type          │                   │
│  │  • parent_id     │  └──────────────────┘                   │
│  └──────────────────┘                                          │
│                                                                 │
│  ┌──────────────────────────────────────┐                      │
│  │      Stored Procedures               │                      │
│  │  • sp_unpaid_students_report(...)    │                      │
│  │  • sp_attendance_summary(...)        │                      │
│  │  • sp_grade_report(...)              │                      │
│  └──────────────────────────────────────┘                      │
└─────────────────────────────────────────────────────────────────┘
```

## Component Interaction Flow

### 1. Page Load Sequence

```
User Opens Page
      │
      ├─► DynamicReportForm.initialize()
      │         │
      │         ├─► ReportApiService.fetchReports()
      │         │         │
      │         │         └─► GET /api/reports
      │         │
      │         └─► renderCategoryTabs(categories)
      │                   │
      │                   └─► Populate UI with categories
      │
      └─► User sees category tabs and report selector
```

### 2. Report Selection Flow

```
User Selects Report
      │
      ├─► handleReportSelection(reportId)
      │         │
      │         ├─► ReportApiService.fetchParameters(reportId)
      │         │         │
      │         │         └─► GET /api/reports/{id}/parameters
      │         │
      │         ├─► DependencyHandler.registerDependencies(parameters)
      │         │
      │         └─► renderForm(parameters)
      │                   │
      │                   ├─► renderParameter() for each parameter
      │                   │         │
      │                   │         ├─► renderDatePicker()
      │                   │         ├─► renderDropdown()
      │                   │         ├─► renderTextInput()
      │                   │         └─► etc.
      │                   │
      │                   └─► attachDependencyListeners()
      │
      └─► User sees dynamic form
```

### 3. Cascading Dropdown Flow

```
User Selects Class
      │
      ├─► Parent Field 'change' event
      │         │
      │         └─► DependencyHandler.handleParentChange()
      │                   │
      │                   ├─► setFieldLoading(true) on Section dropdown
      │                   │
      │                   ├─► ReportApiService.fetchDependentValues(parameterId, classId)
      │                   │         │
      │                   │         └─► GET /api/reports/parameters/{id}/dependent-values?parent_value=X
      │                   │
      │                   ├─► updateFieldOptions(sectionField, values)
      │                   │
      │                   └─► setFieldLoading(false)
      │
      └─► Section dropdown populated with values
```

### 4. Form Submission Flow

```
User Clicks "Generate Report"
      │
      ├─► handleGenerateReport()
      │         │
      │         ├─► FormValidation.validateForm()
      │         │         │
      │         │         ├─► validateField() for each field
      │         │         │         │
      │         │         │         ├─► Check required
      │         │         │         ├─► Check type format
      │         │         │         └─► Return errors
      │         │         │
      │         │         └─► Show errors OR continue
      │         │
      │         ├─► collectFormData(form)
      │         │
      │         ├─► ReportApiService.executeReport(reportId, formData)
      │         │         │
      │         │         └─► POST /api/reports/{id}/execute
      │         │                   │
      │         │                   ├─► Validate parameters
      │         │                   ├─► Execute stored procedure
      │         │                   └─► Return results
      │         │
      │         └─► displayResults(results)
      │                   │
      │                   └─► generateResultsTable()
      │
      └─► User sees report results
```

### 5. Export Flow

```
User Clicks "Export Excel"
      │
      ├─► handleExportReport('excel')
      │         │
      │         ├─► setButtonLoading(true)
      │         │
      │         ├─► ReportApiService.exportReport(reportId, 'excel', formData)
      │         │         │
      │         │         └─► POST /api/reports/{id}/export/excel
      │         │                   │
      │         │                   ├─► Execute report
      │         │                   ├─► Generate Excel file
      │         │                   └─► Return blob
      │         │
      │         ├─► downloadFile(blob, filename)
      │         │         │
      │         │         └─► Create download link and click
      │         │
      │         └─► setButtonLoading(false)
      │
      └─► File downloaded to user's device
```

## Data Flow Diagram

```
┌──────────┐
│  User    │
└────┬─────┘
     │ 1. Selects report
     ▼
┌─────────────────┐
│ Frontend UI     │
└────┬────────────┘
     │ 2. Fetch parameters
     ▼
┌─────────────────┐     3. API Request     ┌──────────────┐
│ API Service     │ ───────────────────► │  Laravel API │
└────┬────────────┘                        └──────┬───────┘
     │ 4. Return parameters                      │
     │◄──────────────────────────────────────────┘
     │                                             5. Query DB
     ▼                                             ▼
┌─────────────────┐                        ┌──────────────┐
│ Form Generator  │                        │   Database   │
└────┬────────────┘                        └──────────────┘
     │ 6. Render form
     ▼
┌─────────────────┐
│ Dynamic Form    │
└────┬────────────┘
     │ 7. User fills & submits
     ▼
┌─────────────────┐
│  Validation     │
└────┬────────────┘
     │ 8. Valid? → Execute report
     ▼
┌─────────────────┐     9. Execute          ┌──────────────┐
│ API Service     │ ───────────────────► │  Laravel API │
└────┬────────────┘                        └──────┬───────┘
     │ 10. Return results                        │
     │◄──────────────────────────────────────────┘
     │                                             11. Run stored proc
     ▼                                             ▼
┌─────────────────┐                        ┌──────────────┐
│ Results Display │                        │   Database   │
└─────────────────┘                        └──────────────┘
```

## Dependency Chain Example

```
Academic Year Dropdown
    │
    │ User selects: 2024-2025
    │
    ├─► Triggers AJAX call
    │
    └─► Class Dropdown loads
          │  Options: Grade 1, Grade 2, Grade 3
          │
          │ User selects: Grade 2
          │
          ├─► Triggers AJAX call
          │
          └─► Section Dropdown loads
                │  Options: Section A, Section B
                │
                │ User selects: Section A
                │
                ├─► Triggers AJAX call
                │
                └─► Student Dropdown loads
                      │  Options: Student 1, Student 2, ...
                      │
                      └─► User selects student
```

## Security Flow

```
┌──────────────┐
│  User Input  │
└──────┬───────┘
       │
       ├─► escapeHtml() → Prevent XSS
       │
       ├─► Client Validation → Better UX
       │
       ▼
┌──────────────┐
│  API Request │
└──────┬───────┘
       │
       ├─► CSRF Token → Prevent CSRF
       │
       ├─► HTTPS → Encrypt data
       │
       ▼
┌──────────────┐
│ Laravel API  │
└──────┬───────┘
       │
       ├─► Server Validation → Security
       │
       ├─► Authentication → Verify user
       │
       ├─► Authorization → Check permissions
       │
       ▼
┌──────────────┐
│   Database   │
└──────────────┘
```

## File Structure

```
new_school_system/
│
├─── resources/
│    ├─── views/
│    │    └─── reports/
│    │         └─── index.blade.php ············ Main UI template
│    │
│    └─── js/
│         ├─── components/
│         │    ├─── DynamicReportForm.js ······ Main component
│         │    └─── DependencyHandler.js ······ Cascading dropdowns
│         │
│         ├─── services/
│         │    └─── ReportApiService.js ········ API communication
│         │
│         └─── utils/
│              └─── FormValidation.js ·········· Client validation
│
├─── docs/
│    ├─── DYNAMIC_REPORT_FORM_DOCUMENTATION.md · Full documentation
│    ├─── DYNAMIC_FORM_QUICK_START.md ·········· Quick start guide
│    ├─── IMPLEMENTATION_SUMMARY.md ············ Implementation summary
│    └─── ARCHITECTURE_DIAGRAM.md ·············· This file
│
└─── vite.config.js ························· Build configuration
```

## Component Responsibilities

### DynamicReportForm.js
- **Primary Role**: Main orchestrator
- **Responsibilities**:
  - Initialize application
  - Render UI components
  - Handle user interactions
  - Coordinate other components
  - Display results

### DependencyHandler.js
- **Primary Role**: Manage cascading dropdowns
- **Responsibilities**:
  - Register dependencies
  - Track parent-child relationships
  - Load dependent values
  - Handle multi-level chains
  - Reset dependent fields

### ReportApiService.js
- **Primary Role**: API communication
- **Responsibilities**:
  - Make HTTP requests
  - Handle responses
  - Manage CSRF tokens
  - Handle errors
  - Download files

### FormValidation.js
- **Primary Role**: Client-side validation
- **Responsibilities**:
  - Validate fields
  - Show error messages
  - Type-specific checks
  - Accessibility support
  - Real-time feedback

## State Management

```
Application State
├─── currentReport (Report object)
├─── currentParameters (Parameter array)
├─── formData (Object with values)
├─── dependencies (Map of relationships)
└─── validationErrors (Error object)

Component State
├─── DynamicReportForm
│    ├─── selectedReportId
│    ├─── formRendered (boolean)
│    └─── resultsDisplayed (boolean)
│
├─── DependencyHandler
│    ├─── dependencies (Map)
│    └─── dependencyChain (Map)
│
└─── FormValidation
     └─── validationRules (Object)
```

## Event Flow

```
User Events                Component Events            API Events
    │                            │                         │
    ├─ Click                     │                         │
    ├─ Change                    ├─ beforeRender           │
    ├─ Blur                      ├─ afterRender            │
    ├─ Submit                    ├─ beforeValidate         │
    │                            ├─ afterValidate          │
    │                            ├─ beforeExecute          │
    │                            ├─ afterExecute           ├─ Request
    │                            │                         ├─ Response
    │                            │                         └─ Error
    │                            │
    └──────────────────────────►└─────────────────────────►
```

---

## Summary

This architecture provides:

- **Separation of Concerns**: Clear component boundaries
- **Scalability**: Easy to add new features
- **Maintainability**: Well-organized code structure
- **Testability**: Independent components
- **Performance**: Optimized data flow
- **Security**: Multiple security layers
- **Accessibility**: Built-in WCAG compliance

The system is designed to be **production-ready**, **maintainable**, and **extensible** for future enhancements.
