# Dynamic Reports Module

## Overview

This module provides a flexible, production-ready system for generating dynamic reports with parameter-driven forms, cascading dropdowns, and multiple export formats.

## Features

- Dynamic form generation from API metadata
- 8+ parameter types (date, text, number, select, multiselect, checkbox, textarea)
- Cascading dropdowns with multi-level dependencies
- Client-side validation with accessibility support
- Real-time form updates
- Report execution with responsive results display
- Export to Excel, PDF, and CSV formats
- Mobile-responsive Bootstrap 5 design
- WCAG 2.1 AA compliant

## Quick Start

### 1. Access the Reports Page

```
Navigate to: /reports
```

### 2. Select a Category

Click on a category tab to filter reports by type (Financial, Academic, Attendance, etc.)

### 3. Choose a Report

Select a report from the dropdown to load its parameters.

### 4. Fill Parameters

- **Required fields** are marked with a red asterisk (*)
- **Dependent dropdowns** will load automatically when parent values are selected
- **Validation** happens in real-time when you leave a field

### 5. Generate Report

Click "Generate Report" to execute the report with your parameters.

### 6. Export Results

Click Excel, PDF, or CSV export buttons to download the report.

## Parameter Types

| Type | Description | Example |
|------|-------------|---------|
| `date` | Date picker | Select start date |
| `text` | Single-line text | Enter student name |
| `email` | Email input | user@example.com |
| `number` | Numeric input | Enter age |
| `textarea` | Multi-line text | Enter notes |
| `select` | Dropdown (single) | Select class |
| `multiselect` | Multi-select | Select subjects |
| `checkbox` | Boolean option | Include archived |

## Cascading Dropdowns

Some parameters depend on others. For example:

1. Select **Academic Year** first
2. Then **Class** dropdown loads classes for that year
3. Then **Section** dropdown loads sections for that class
4. Finally **Student** dropdown loads students in that section

Dependent dropdowns are disabled until their parent value is selected.

## Validation

- **Required fields**: Must have a value
- **Date fields**: Must be valid dates
- **Number fields**: Must be numeric
- **Email fields**: Must be valid email format

Errors display below fields with clear messages.

## Export Formats

### Excel (.xlsx)
- Best for data analysis and manipulation
- Preserves formatting and formulas
- Opens in Microsoft Excel, Google Sheets, etc.

### PDF (.pdf)
- Best for printing and sharing
- Fixed layout and formatting
- Universal viewing compatibility

### CSV (.csv)
- Best for data import/export
- Plain text format
- Compatible with all spreadsheet software

## Keyboard Navigation

- **Tab**: Move between fields
- **Shift + Tab**: Move backward
- **Enter**: Submit form (when on button)
- **Arrow Keys**: Navigate dropdowns
- **Escape**: Close dropdowns

## Accessibility Features

- Screen reader compatible
- Keyboard navigation support
- ARIA labels and descriptions
- Clear error announcements
- High contrast support
- Focus indicators

## Troubleshooting

### Form Not Loading

**Problem**: Form doesn't appear after selecting report
**Solution**: Check browser console for errors, refresh page

### Dropdown Empty

**Problem**: Dependent dropdown shows no options
**Solution**: Ensure parent dropdown has a selected value

### Validation Error

**Problem**: Form won't submit with validation errors
**Solution**: Check error messages below fields, correct invalid values

### Export Fails

**Problem**: Export button doesn't download file
**Solution**: Ensure report has been generated first

## Technical Details

### Frontend Stack
- JavaScript ES6+ Modules
- Bootstrap 5
- Vite bundler
- Vanilla JavaScript (no jQuery dependency)

### API Endpoints
- `GET /api/reports` - List reports
- `GET /api/reports/{id}/parameters` - Get parameters
- `GET /api/reports/parameters/{id}/dependent-values` - Get dependent values
- `POST /api/reports/{id}/execute` - Execute report
- `POST /api/reports/{id}/export/{format}` - Export report

### Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers

## Files

```
resources/views/reports/
├── index.blade.php ················ Main reports page
└── README.md ·················· This file

resources/js/
├── components/
│   ├── DynamicReportForm.js ······· Main form component
│   └── DependencyHandler.js ······· Cascading dropdowns
├── services/
│   └── ReportApiService.js ········ API communication
└── utils/
    └── FormValidation.js ·········· Client validation

docs/
├── DYNAMIC_REPORT_FORM_DOCUMENTATION.md · Full technical docs
├── DYNAMIC_FORM_QUICK_START.md ·········· Implementation guide
├── IMPLEMENTATION_SUMMARY.md ············ Feature summary
└── ARCHITECTURE_DIAGRAM.md ·············· System architecture
```

## For Developers

### Adding New Parameter Types

Edit `DynamicReportForm.js`:

```javascript
renderParameterInput(param, baseAttrs) {
    switch (param.type) {
        case 'your_new_type':
            return this.renderYourNewType(param, baseAttrs);
        // ... existing cases
    }
}

renderYourNewType(param, baseAttrs) {
    return `
        <input
            type="..."
            class="form-control"
            ${baseAttrs}
        />
    `;
}
```

### Customizing Validation

Edit `FormValidation.js`:

```javascript
validateField(field, isRequired, type) {
    // Add custom validation logic
    if (type === 'your_new_type') {
        return this.validateYourNewType(field);
    }
    // ... existing validation
}
```

### Styling Customization

Override CSS in your stylesheet:

```css
.parameter-field {
    /* Your custom styles */
}

.dynamic-form-container {
    /* Your custom layout */
}
```

## Support

For detailed documentation, see:
- `/docs/DYNAMIC_REPORT_FORM_DOCUMENTATION.md` - Complete technical documentation
- `/docs/DYNAMIC_FORM_QUICK_START.md` - Implementation guide
- `/docs/IMPLEMENTATION_SUMMARY.md` - Feature summary
- `/docs/ARCHITECTURE_DIAGRAM.md` - System architecture

For issues:
1. Check browser console for JavaScript errors
2. Check network tab for failed API requests
3. Verify backend API responses
4. Review Laravel logs for server errors

---

**Version**: 1.0.0
**Last Updated**: 2025-10-11
