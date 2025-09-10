# Fee System Integration Guide

## Overview

The school management system now supports both **Legacy Fee System** and **Enhanced Service-Based Fee System** running in parallel. This guide explains how to switch between systems, migrate data, and manage the transition.

## System Architecture

### Dual Service Architecture

```
FeesServiceManager
├── LegacyService (FeesGenerationService)
└── EnhancedService (EnhancedFeesGenerationService)
```

### Key Components

1. **FeesServiceManager**: Central service that routes requests to appropriate system
2. **Configuration System**: Database and config-based system selection
3. **Migration System**: Safe data migration with rollback capability
4. **Validation System**: Ensures system readiness before switching

## Migration Status Resolution

### Problem
The migrations don't appear in `php artisan migrate:status` because they're tenant-specific migrations.

### Solution
Use the correct commands for tenant migrations:

```bash
# Check tenant migration status
php artisan migrate:status --path=database/migrations/tenant

# Run tenant migrations
php artisan migrate --path=database/migrations/tenant

# Fresh migration with seeding (single school setup)
php artisan migrate:fresh --seed --path=database/migrations/tenant
```

## Service Integration Resolution

### Problem
The existing `FeesGenerationService` is 561 lines of complex legacy code deeply integrated with the old fee assignment system.

### Solution
Created separate services with a service manager for safe transition:

1. **Keep Legacy Service Intact**: No modifications to existing `FeesGenerationService`
2. **New Enhanced Service**: `EnhancedFeesGenerationService` for service-based system
3. **Service Manager**: `FeesServiceManager` routes to appropriate service
4. **Configuration Control**: Database setting controls which system is active

## System Switching

### Prerequisites

Before switching to the enhanced system:

1. **Run Migrations**: All tenant migrations must be completed
2. **Verify Tables**: Required tables and columns must exist
3. **Data Migration**: Existing fee data should be migrated (optional but recommended)

### Switching Process

#### 1. Check System Status

```bash
# API endpoint
GET /fees-generation/system-status
```

Response example:
```json
{
  "success": true,
  "data": {
    "compatibility_report": {
      "current_system": "legacy",
      "enhanced_system_ready": true,
      "migration_status": {
        "total_migrations": 5,
        "completed_migrations": 5,
        "pending_migrations": 0,
        "is_complete": true
      },
      "data_compatibility": {
        "has_legacy_data": true,
        "has_enhanced_data": false,
        "is_migrated": true
      },
      "recommendations": []
    },
    "usage_statistics": {
      "legacy_system": {
        "total_generations": 150,
        "total_collections": 5000
      },
      "enhanced_system": {
        "total_services": 0,
        "total_collections": 0
      }
    }
  }
}
```

#### 2. Switch System

```bash
# Switch to enhanced system
POST /fees-generation/switch-system
{
  "system": "enhanced"
}

# Switch back to legacy system
POST /fees-generation/switch-system
{
  "system": "legacy"
}
```

#### 3. Validate Switch

The system automatically validates before switching:
- Required migrations are complete
- Required tables exist
- Required columns are present
- Data compatibility is checked

## Configuration Options

### Environment Variables

```env
# Enable enhanced system by default
USE_ENHANCED_FEE_SYSTEM=false
```

### Database Settings

```php
// Enable enhanced system
setting(['use_enhanced_fee_system' => true]);

// Check current system
$isEnhanced = setting('use_enhanced_fee_system', false);
```

### Config File

Create or update `config/fees.php`:

```php
return [
    'use_enhanced_system' => env('USE_ENHANCED_FEE_SYSTEM', false),
    'academic_levels' => ['kg', 'primary', 'secondary', 'high_school', 'all'],
    'fee_categories' => ['academic', 'transport', 'meal', 'accommodation', 'activity', 'other'],
    // ... more configuration
];
```

## API Endpoints

### New Service Manager Endpoints

```php
// System status and compatibility
GET /fees-generation/system-status

// Switch between systems
POST /fees-generation/switch-system
Body: {"system": "enhanced|legacy"}

// Generate preview with active service
POST /fees-generation/preview-managed
Body: {standard preview parameters}

// Generate fees with active service  
POST /fees-generation/generate-managed
Body: {standard generation parameters}
```

### Response Format

All new endpoints include `active_system` in responses:

```json
{
  "success": true,
  "data": {
    // ... response data
    "active_system": "enhanced"
  }
}
```

## Migration Process

### 1. Run Required Migrations

```bash
# Check status first
php artisan migrate:status --path=database/migrations/tenant

# Run migrations
php artisan migrate --path=database/migrations/tenant
```

### 2. Migrate Existing Data (Optional)

The data migration is included in the migrations:
```bash
# This migration will:
# - Enhance fees_types table
# - Create student_services table
# - Create academic_level_configs table
# - Migrate existing fee data to new structure
2025_01_09_130000_migrate_existing_fee_data_to_service_structure
```

### 3. Verify Migration

```bash
# Check system status via API
GET /fees-generation/system-status
```

## Rollback Process

### Emergency Rollback

If issues occur after switching to enhanced system:

```bash
# Switch back to legacy system
POST /fees-generation/switch-system
Body: {"system": "legacy"}
```

### Data Rollback

The migration includes rollback functionality:

```bash
# Rollback specific migration if needed
php artisan migrate:rollback --path=database/migrations/tenant --step=1
```

## Testing the Integration

### 1. Test System Status

```bash
curl -X GET "http://your-domain/fees-generation/system-status" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Test System Switch

```bash
# Switch to enhanced
curl -X POST "http://your-domain/fees-generation/switch-system" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"system": "enhanced"}'

# Verify switch worked
curl -X GET "http://your-domain/fees-generation/system-status" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Test Fee Generation

```bash
# Generate preview with active service
curl -X POST "http://your-domain/fees-generation/preview-managed" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "classes": [1, 2],
    "month": 1,
    "year": 2025
  }'
```

## Troubleshooting

### Common Issues

#### 1. Migrations Not Showing
**Problem**: `php artisan migrate:status` doesn't show new migrations
**Solution**: Use `--path=database/migrations/tenant` flag

#### 2. Enhanced System Not Ready
**Problem**: System validation fails when switching
**Solution**: Check missing migrations or tables:

```bash
# Check specific migration status
php artisan migrate:status --path=database/migrations/tenant | grep 2025_01_09
```

#### 3. Data Compatibility Issues
**Problem**: Legacy data not visible in enhanced system
**Solution**: Run data migration:

```bash
# Run the specific data migration
php artisan migrate --path=database/migrations/tenant --step=1 2025_01_09_130000_migrate_existing_fee_data_to_service_structure
```

### Validation Errors

The system provides detailed validation messages:

```json
{
  "success": false,
  "message": "Cannot switch to enhanced system.",
  "errors": [
    "Enhanced system is not ready. Please run migrations first.",
    "Required table 'student_services' does not exist."
  ]
}
```

## Best Practices

### 1. Gradual Migration
- Start with enhanced system disabled
- Run migrations in staging environment first
- Test thoroughly before switching in production
- Keep legacy system as fallback

### 2. Data Safety
- Always backup database before migrations
- Test rollback process in staging
- Monitor system after switching
- Keep migration logs for audit trail

### 3. User Training
- Train staff on new system features
- Document new workflows
- Provide comparison guides
- Plan transition timeline

## Support and Monitoring

### Logging

The system logs all switches and important operations:

```php
// System switch logging
\Log::info('Enhanced fee system enabled', [
    'user_id' => auth()->id(),
    'timestamp' => now()
]);
```

### Monitoring

Monitor these metrics after switching:
- Fee generation success rates
- System response times
- Error rates and types
- User adoption metrics

### Support Contacts

For technical issues:
1. Check system logs first
2. Verify migration status
3. Test rollback if needed
4. Contact system administrator

## Future Considerations

### Deprecation Timeline
- Legacy system will be maintained for 6 months after enhanced system is stable
- After 6 months, legacy system will enter maintenance-only mode
- After 12 months, legacy system may be deprecated (with notice)

### Feature Parity
The enhanced system provides:
- ✅ All legacy system features
- ✅ Improved service-based subscriptions
- ✅ Advanced discount system
- ✅ Better academic level detection
- ✅ Automated fee generation
- ✅ Enhanced reporting capabilities

### Migration Path
1. **Phase 1**: Parallel operation (current)
2. **Phase 2**: Enhanced system as default (after testing)
3. **Phase 3**: Legacy system deprecation warning
4. **Phase 4**: Legacy system removal (with migration requirement)