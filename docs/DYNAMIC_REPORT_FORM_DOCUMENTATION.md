# Dynamic Report Form System - Frontend Documentation

## Overview

The Dynamic Report Form system provides a flexible, production-ready frontend solution for rendering dynamic forms based on backend parameter definitions. Built with vanilla JavaScript/jQuery and Bootstrap 5, it seamlessly integrates with Laravel Blade templates.

## Architecture

### Component Structure

```
resources/js/
├── components/
│   ├── DynamicReportForm.js      # Main form component
│   └── DependencyHandler.js      # Cascading dropdown handler
├── services/
│   └── ReportApiService.js       # API communication layer
└── utils/
    └── FormValidation.js         # Client-side validation
```

### Technology Stack

- **Framework**: Vanilla JavaScript ES6+ Modules
- **UI Framework**: Bootstrap 5
- **Bundler**: Vite
- **Notifications**: Toastr.js (optional)
- **Backend**: Laravel Blade + JSON API

## Features

### Core Capabilities

1. **Dynamic Form Generation**
   - Automatic form rendering from API metadata
   - Support for multiple input types (date, text, number, select, multiselect, checkbox)
   - Responsive grid layout with Bootstrap 5

2. **Cascading Dropdowns**
   - Parent-child parameter dependencies
   - Multi-level dependency chains
   - Automatic value loading via AJAX
   - Loading states and error handling

3. **Client-Side Validation**
   - Required field validation
   - Type-specific validation (date, number, email)
   - Real-time validation on blur
   - Inline error messages with accessibility support

4. **Report Execution & Export**
   - Generate reports with parameters
   - Display results in responsive tables
   - Export to Excel, PDF, CSV formats
   - File download handling

5. **Accessibility**
   - ARIA labels and attributes
   - Keyboard navigation support
   - Screen reader friendly error messages
   - Semantic HTML structure

6. **User Experience**
   - Loading states for async operations
   - Clear visual feedback
   - Smooth transitions
   - Mobile-responsive design

## API Contract

### Expected Backend Endpoints

#### 1. Fetch All Reports
```
GET /api/reports
```

**Response:**
```json
{
  "success": true,
  "categories": [
    {
      "id": 1,
      "name": "Financial Reports",
      "reports": [
        {
          "id": 1,
          "name": "Unpaid Students Report",
          "description": "Lists students with unpaid fees"
        }
      ]
    }
  ]
}
```

#### 2. Fetch Report Parameters
```
GET /api/reports/{reportId}/parameters
```

**Response:**
```json
{
  "success": true,
  "report": {
    "id": 1,
    "name": "Unpaid Students Report",
    "description": "Lists students with unpaid fees"
  },
  "parameters": [
    {
      "id": 1,
      "name": "p_start_date",
      "label": "Start Date",
      "type": "date",
      "is_required": 1,
      "default_value": "2025-10-11",
      "description": "Filter start date",
      "placeholder": "Select start date"
    },
    {
      "id": 2,
      "name": "p_class_id",
      "label": "Class",
      "type": "select",
      "is_required": 0,
      "values": [
        {"value": 1, "label": "Grade 1"},
        {"value": 2, "label": "Grade 2"}
      ]
    },
    {
      "id": 3,
      "name": "p_section_id",
      "label": "Section",
      "type": "select",
      "parent_id": 2,
      "depends_on": "p_class_id",
      "is_required": 0
    }
  ]
}
```

#### 3. Fetch Dependent Values
```
GET /api/reports/parameters/{parameterId}/dependent-values?parent_value={value}
```

**Response:**
```json
{
  "success": true,
  "values": [
    {"value": 1, "label": "Section A"},
    {"value": 2, "label": "Section B"}
  ]
}
```

#### 4. Execute Report
```
POST /api/reports/{reportId}/execute
Content-Type: application/json

{
  "p_start_date": "2025-10-11",
  "p_class_id": 1,
  "p_section_id": 2
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "student_name": "John Doe",
      "enrollment_number": "STU001",
      "amount_due": "500.00"
    }
  ],
  "columns": [
    {"name": "student_name", "label": "Student Name"},
    {"name": "enrollment_number", "label": "Enrollment Number"},
    {"name": "amount_due", "label": "Amount Due"}
  ]
}
```

#### 5. Export Report
```
POST /api/reports/{reportId}/export/{format}
Content-Type: application/json

{
  "p_start_date": "2025-10-11",
  "p_class_id": 1
}
```

**Response:**
- Binary file download (Excel, PDF, or CSV)
- Content-Disposition header with filename

## Parameter Types

### Supported Types

| Type | HTML Element | Description |
|------|--------------|-------------|
| `date` | `<input type="date">` | HTML5 date picker |
| `text` | `<input type="text">` | Single-line text input |
| `email` | `<input type="email">` | Email input with validation |
| `number` | `<input type="number">` | Numeric input with min/max |
| `textarea` | `<textarea>` | Multi-line text input |
| `select` | `<select>` | Dropdown select (single) |
| `multiselect` | `<select multiple>` | Multi-select dropdown |
| `checkbox` | `<input type="checkbox">` | Boolean checkbox |

### Parameter Object Schema

```typescript
interface Parameter {
  id: number;
  name: string;              // Parameter name (e.g., p_start_date)
  label: string;             // Display label
  type: string;              // Parameter type (date, text, etc.)
  is_required: 0 | 1;        // Required flag
  default_value?: any;       // Default value
  description?: string;      // Help text
  placeholder?: string;      // Input placeholder

  // For select/multiselect
  values?: Array<{value: any, label: string}>;

  // For number inputs
  min_value?: number;
  max_value?: number;
  step?: number;

  // For dependencies
  depends_on?: string;       // Parent parameter name
  parent_id?: number;        // Parent parameter ID

  // For textarea
  rows?: number;
}
```

## Usage Examples

### Basic Implementation

```blade
<!-- In your Blade template -->
@extends('backend.master')

@section('content')
<div id="reportContainer">
    <select id="reportSelector"></select>
    <div id="dynamicFormContainer"></div>
    <button id="generateReportBtn">Generate Report</button>
</div>
@endsection

@push('script')
<script type="module">
    import { DynamicReportForm } from '{{ asset('js/components/DynamicReportForm.js') }}';

    const reportForm = new DynamicReportForm({
        reportSelectorId: 'reportSelector',
        formContainerId: 'dynamicFormContainer',
        generateBtnId: 'generateReportBtn'
    });

    reportForm.initialize();
</script>
@endpush
```

### Custom Validation

```javascript
// Add custom validation rule
FormValidation.validateField = function(field, isRequired, type) {
    const result = { isValid: true, error: null };

    // Your custom validation logic
    if (field.value === 'forbidden') {
        result.isValid = false;
        result.error = 'This value is not allowed';
    }

    return result;
};
```

### Handling Custom Events

```javascript
// Listen to report generation
document.addEventListener('reportGenerated', (event) => {
    console.log('Report generated:', event.detail);
});

// Customize form rendering
const reportForm = new DynamicReportForm({
    // ... config
});

reportForm.on('beforeRender', (parameters) => {
    // Modify parameters before rendering
    console.log('About to render:', parameters);
});
```

## Dependency System

### How Dependencies Work

1. **Registration**: Parameters with `depends_on` and `parent_id` are registered as dependent fields
2. **Initialization**: Dependent fields are disabled until parent has a value
3. **Parent Change**: When parent value changes, dependent field makes AJAX call
4. **Value Loading**: New options are loaded and field is enabled
5. **Chain Propagation**: Changes cascade down multi-level dependencies

### Example Dependency Chain

```
Academic Year (p_academic_year)
  └─ Class (p_class_id, depends on p_academic_year)
      └─ Section (p_section_id, depends on p_class_id)
          └─ Student (p_student_id, depends on p_section_id)
```

### Backend Implementation for Dependencies

```php
// In your controller
public function getDependentValues(Request $request, $parameterId)
{
    $parameter = ReportParameter::findOrFail($parameterId);
    $parentValue = $request->input('parent_value');

    // Example: Load sections based on class_id
    if ($parameter->name === 'p_section_id') {
        $sections = ClassSection::where('class_id', $parentValue)
            ->get(['id as value', 'name as label']);

        return response()->json([
            'success' => true,
            'values' => $sections
        ]);
    }
}
```

## Validation

### Client-Side Validation Rules

| Rule | Trigger | Description |
|------|---------|-------------|
| Required | Form submit, blur | Checks if field has value |
| Date Format | Form submit, blur | Validates date format |
| Number Format | Form submit, blur | Validates numeric input |
| Email Format | Form submit, blur | Validates email pattern |
| Min/Max | Form submit, blur | Checks number range |

### Validation Flow

```
User Input → Blur Event → Validate Field → Show/Hide Error
                                           ↓
                                    Update UI State
                                           ↓
                                    Set ARIA Attributes
```

### Custom Validation Messages

```javascript
window.ReportConfig = {
    translations: {
        requiredField: 'This field is required',
        invalidFormat: 'Invalid format',
        invalidEmail: 'Please enter a valid email',
        // ... more custom messages
    }
};
```

## Styling Customization

### CSS Variables

```css
:root {
    --report-primary-color: #0d6efd;
    --report-error-color: #dc3545;
    --report-border-radius: 0.375rem;
    --report-form-spacing: 1rem;
}
```

### Custom Classes

```css
/* Override parameter field styling */
.parameter-field {
    margin-bottom: 1.5rem;
}

/* Customize loading state */
.dynamic-form-container.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Style dependent fields */
.dependent-field .spinner-border {
    color: #0d6efd;
}
```

## Accessibility Features

### ARIA Support

- `aria-required="true"` on required fields
- `aria-invalid="true"` on invalid fields
- `aria-describedby` linking to error messages
- `role="status"` on loading indicators
- `aria-label` on interactive elements

### Keyboard Navigation

- Tab order follows visual flow
- Enter key submits form
- Escape key closes modals
- Arrow keys navigate dropdowns

### Screen Reader Announcements

```javascript
// Announce validation errors
field.setAttribute('aria-live', 'polite');
field.setAttribute('aria-atomic', 'true');
```

## Error Handling

### Error Types

1. **Network Errors**: API connection failures
2. **Validation Errors**: Client-side validation failures
3. **Server Errors**: Backend processing errors
4. **Dependency Errors**: Failed to load dependent values

### Error Display

- **Inline Errors**: Displayed below field
- **Toast Notifications**: Global error messages
- **Validation Summary**: List of all errors

### Error Recovery

```javascript
try {
    await apiService.executeReport(reportId, formData);
} catch (error) {
    // Log error for debugging
    console.error('Report execution failed:', error);

    // Show user-friendly message
    showError('Failed to generate report. Please try again.');

    // Attempt recovery
    if (error.status === 401) {
        redirectToLogin();
    }
}
```

## Performance Optimization

### Implemented Optimizations

1. **Lazy Loading**: Load parameters only when report is selected
2. **Debouncing**: Debounce dependency API calls
3. **Caching**: Cache report metadata
4. **Code Splitting**: Separate chunks for components and services
5. **Minimal DOM Updates**: Update only changed elements

### Performance Metrics

| Operation | Target Time |
|-----------|-------------|
| Form Render | < 200ms |
| API Call | < 1s |
| Validation | < 50ms |
| Export Download | < 3s |

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari 14+, Chrome Mobile)

## Troubleshooting

### Common Issues

**Issue**: Form not rendering
- **Solution**: Check API response format matches expected schema
- **Debug**: Open browser console and check for errors

**Issue**: Dependent dropdown not loading
- **Solution**: Verify `depends_on` and `parent_id` are correctly set
- **Debug**: Check network tab for API calls

**Issue**: Validation not working
- **Solution**: Ensure `data-required` and `data-parameter-type` attributes are set
- **Debug**: Check validation rules in FormValidation.js

**Issue**: Export not working
- **Solution**: Verify backend returns proper Content-Disposition header
- **Debug**: Check response headers in network tab

## Testing

### Manual Testing Checklist

- [ ] Form renders correctly for all parameter types
- [ ] Required field validation works
- [ ] Type-specific validation works (date, number, email)
- [ ] Dependent dropdowns load correctly
- [ ] Multi-level dependencies cascade properly
- [ ] Report generates with valid parameters
- [ ] Export to Excel/PDF/CSV works
- [ ] Error messages display correctly
- [ ] Loading states show appropriately
- [ ] Mobile responsive design works
- [ ] Keyboard navigation functions
- [ ] Screen reader compatibility

### Automated Testing

```javascript
// Example unit test
describe('FormValidation', () => {
    it('should validate required fields', () => {
        const field = document.createElement('input');
        field.value = '';

        const result = FormValidation.validateField(field, true, 'text');

        expect(result.isValid).toBe(false);
        expect(result.error).toBe('This field is required');
    });
});
```

## Security Considerations

### XSS Prevention

- All user input is escaped using `escapeHtml()` method
- HTML content is sanitized before rendering
- CSP headers recommended

### CSRF Protection

- CSRF token included in all API requests
- Token retrieved from meta tag or config

### Data Validation

- Client-side validation for UX
- **Server-side validation is mandatory** (never trust client)

## Future Enhancements

### Planned Features

1. **Advanced Input Types**
   - Color picker
   - File upload
   - Rich text editor
   - Date range picker

2. **Enhanced Dependencies**
   - Complex conditional logic
   - Multiple parent dependencies
   - Dynamic visibility rules

3. **Report Scheduling**
   - Schedule report generation
   - Email delivery
   - Recurring reports

4. **Data Visualization**
   - Chart integration
   - Dashboard widgets
   - Interactive graphs

5. **Offline Support**
   - Service worker caching
   - Offline form drafts
   - Background sync

## License & Credits

Built for the School Management System project using:
- Laravel Framework
- Bootstrap 5
- Vite
- Toastr.js

## Support

For issues or questions:
- Check this documentation
- Review browser console for errors
- Verify API responses match expected format
- Check Laravel logs for backend errors

---

**Version**: 1.0.0
**Last Updated**: 2025-10-11
**Author**: Frontend Development Team
