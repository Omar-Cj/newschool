# Export Service - Quick Reference Card

## Installation Checklist

- [x] All packages installed (maatwebsite/excel, dompdf)
- [ ] Routes registered in `routes/web.php`
- [ ] Queue migrations run (`php artisan queue:table && migrate`)
- [ ] Queue worker started (`php artisan queue:work`)
- [ ] Scheduled cleanup configured (optional)

## Basic Export Code

```php
use App\Services\ExportService;

$exportService = app(ExportService::class);

return $exportService->export(
    reportId: 123,
    format: 'excel', // 'excel', 'pdf', or 'csv'
    results: $data,
    columns: $columnDefinitions,
    reportMetadata: ['name' => 'Report Name']
);
```

## Column Definition

```php
$columns = [
    ['key' => 'field_name', 'label' => 'Display Name', 'type' => 'column_type'],
];
```

## Column Types

| Type | Use For | Format Example |
|------|---------|----------------|
| `string` | Text | As-is |
| `number` | Numbers | 1,234.56 |
| `currency` | Money | $1,234.56 |
| `percentage` | Percent | 85.5% |
| `date` | Dates | 2025-01-15 |
| `datetime` | Date+Time | 2025-01-15 14:30:00 |
| `boolean` | Yes/No | Yes / No |

## Format Comparison

| Feature | Excel | PDF | CSV |
|---------|-------|-----|-----|
| Max Rows | ∞ | 2,000 | ∞ |
| Formatting | ✓ | ✓ | ✗ |
| File Size | Medium | Large | Small |
| Speed | Fast | Slow | Fastest |

## API Endpoints

```
POST   /reports/{id}/export        - Export report
GET    /reports/download-export    - Download export
POST   /reports/quick-export       - Quick export
GET    /reports/export-options     - Get options
POST   /reports/cleanup-exports    - Cleanup (admin)
```

## Console Commands

```bash
# Clean up old exports
php artisan reports:cleanup-exports

# With options
php artisan reports:cleanup-exports --hours=48 --dry-run

# Start queue worker
php artisan queue:work --queue=exports,exports-heavy
```

## Frontend AJAX Example

```javascript
fetch('/reports/1/export', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        format: 'excel',
        results: data,
        columns: columns,
        metadata: { name: 'Report' }
    })
});
```

## Error Codes

| Code | Meaning | Solution |
|------|---------|----------|
| 400 | Invalid format | Use excel/pdf/csv |
| 403 | Unauthorized | Check permissions |
| 422 | Validation failed | Check request |
| 500 | Export failed | Check logs |

## Performance Tips

1. Use CSV for >100K rows
2. Enable async for >500 rows
3. Chunk database queries
4. Monitor queue workers
5. Clean up old files

## Troubleshooting

| Problem | Fix |
|---------|-----|
| Timeout | Enable async or increase timeout |
| Memory | Increase PHP memory_limit |
| PDF too large | Use Excel/CSV instead |
| Queue stuck | Restart queue worker |
| Download expired | Re-export (24hr limit) |

## Security Checklist

- [ ] Permission middleware applied
- [ ] Multi-tenant filtering active
- [ ] Input validation enabled
- [ ] Audit logging configured
- [ ] CSV injection prevention on

## Common Patterns

### Simple Export
```php
return $exportService->export(1, 'excel', $data, $columns);
```

### With Metadata
```php
return $exportService->export(
    1, 'pdf', $data, $columns,
    ['name' => 'Report', 'parameters' => ['year' => 2024]]
);
```

### Force Async
```php
return $exportService->export(
    1, 'excel', $data, $columns, [], true
);
```

## File Locations

```
app/Services/ExportService.php
app/Exports/DynamicReportExport.php
app/Jobs/GenerateReportExportJob.php
app/Http/Controllers/DynamicReportController.php
app/Notifications/ExportReadyNotification.php
app/Notifications/ExportFailedNotification.php
app/Console/Commands/CleanupReportExports.php
resources/views/reports/pdf/template.blade.php
routes/reports.php
```

## Support Resources

- **Full Docs:** `/docs/EXPORT_SERVICE_DOCUMENTATION.md`
- **Examples:** `/docs/EXPORT_INTEGRATION_EXAMPLE.md`
- **Tests:** `/tests/Feature/ExportServiceTest.php`
- **Logs:** `storage/logs/laravel.log`

## Version Info

- **Created:** 2025-10-11
- **Version:** 1.0.0
- **Laravel:** 12.0+
- **PHP:** 8.2+

---

**Quick Help:**
- Logs: `tail -f storage/logs/laravel.log`
- Queue: `php artisan queue:work --verbose`
- Failed: `php artisan queue:failed`
- Retry: `php artisan queue:retry all`
