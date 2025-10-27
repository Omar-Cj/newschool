# Student Bulk Import Guide (V2)

This guide explains the new improved student bulk import system that matches the individual student creation form.

## Overview

The new bulk import system uses a simplified Excel template with header-based column mapping, making it easier to use and maintain.

## Import Process Flow

1. **Select Grade** - Choose the grade for all students in the import file
2. **Select Class** - Choose the class (filtered by grade if applicable)
3. **Select Section** - Choose the section
4. **Upload Excel** - Upload your Excel file with student data

**Important**: All students in one import batch will be assigned to the same Grade, Class, and Section selected in the form.

## Excel Template Structure

### Required Columns

| Column Name     | Description                           | Example               |
|-----------------|---------------------------------------|-----------------------|
| `first_name`    | Student's first name                  | John                  |
| `last_name`     | Student's last name                   | Doe                   |
| `parent_mobile` | Parent/Guardian mobile number         | 33123456              |

### Optional Columns

| Column Name       | Description                              | Example/Format              |
|-------------------|------------------------------------------|-----------------------------|
| `shift`           | Shift ID from your system                | 1 or 2                      |
| `gender`          | Gender ID (1=Male, 2=Female)             | 1                           |
| `category`        | Student category ID                      | 1, 2, 3, etc.               |
| `mobile`          | Student's mobile number                  | 33234567                    |
| `email`           | Student's email address                  | john.doe@example.com        |
| `username`        | Login username (auto-generated if empty) | john_doe_2024               |
| `date_of_birth`   | Student's birth date                     | 2015-01-15                  |
| `admission_date`  | Admission date (defaults to today)       | 2024-09-01                  |
| `parent_name`     | Parent/Guardian name (if creating new)   | Jane Doe                    |
| `parent_relation` | Relation (Father/Mother/Guardian/Other)  | Mother                      |
| `fee_services`    | Comma-separated optional service IDs     | 3,5,7                       |

## Important Notes

### Parent Handling
- The system will **automatically check** if a parent with the given `parent_mobile` already exists
- If parent exists → Student will be linked to that parent
- If parent doesn't exist → New parent will be created
- This prevents duplicate parent accounts

### Fee Services
- Only **optional fee services** can be assigned via Excel
- **Mandatory services** are automatically assigned based on the student's grade
- Use comma-separated service IDs (e.g., "3,5,7")
- Invalid service IDs will be logged but won't fail the import

### Grade Assignment
- Grade is selected in the import form (NOT in Excel)
- All students in the import file will be assigned the selected grade
- This ensures consistency and reduces errors

### Default Values
- **Username**: Auto-generated if not provided (format: student_timestamp_random)
- **Password**: Default is "123456" for all students
- **Admission Date**: Defaults to today if not provided
- **Status**: All imported students are marked as Active

## Example Excel Data

Here's an example of what your Excel file should look like:

| first_name | last_name | parent_mobile | gender | category | parent_name | parent_relation | fee_services |
|------------|-----------|---------------|--------|----------|-------------|-----------------|--------------|
| John       | Doe       | 33123456      | 1      | 1        | Jane Doe    | Mother          | 3,5          |
| Sarah      | Smith     | 33234567      | 2      | 2        | Bob Smith   | Father          | 3,7          |
| Ahmed      | Ali       | 33123456      |        |          |             |                 | 5            |

**Note**: Ahmed will be linked to the same parent as John (parent_mobile: 33123456)

## Validation

The import system validates:
- ✓ Required fields (first_name, last_name, parent_mobile)
- ✓ Email uniqueness (if provided)
- ✓ Username uniqueness (if provided)
- ✓ Shift/Gender/Category IDs exist in database
- ✓ Date formats are correct
- ✓ Parent relation is valid (Father/Mother/Guardian/Other)

If validation fails, you'll see error messages with **row numbers** to help you fix the issues.

## Error Handling

### Common Errors

1. **"Email already exists"** - Check if email is already used by another student
2. **"Username already exists"** - Leave username empty to auto-generate or provide unique username
3. **"Invalid shift ID"** - Verify shift ID exists in your system
4. **"Invalid gender ID"** - Use 1 for Male, 2 for Female
5. **"Required field missing"** - Check first_name, last_name, and parent_mobile are provided

### Tips for Successful Import

1. **Download the sample file** - Use it as a template
2. **Keep headers exactly as shown** - Column names are case-sensitive
3. **Don't add extra columns** - Only use the columns listed above
4. **Use consistent data** - Grade/Class/Section selected in form applies to ALL rows
5. **Test with small batches first** - Import 2-3 students to test before bulk import
6. **Check parent mobile numbers** - Make sure they're accurate to prevent duplicate parents

## System ID Reference

### Gender IDs
- 1 = Male
- 2 = Female

### Common Relations
- Father
- Mother
- Guardian
- Other

*Note: For other IDs (Shift, Category, Fee Services), refer to your system's admin panel or database.*

## Support

If you encounter issues during import:
1. Check the error messages - they include row numbers
2. Review this guide for correct format
3. Check system logs for detailed error information
4. Contact system administrator if issues persist

---

**Last Updated**: January 2025
**Version**: 2.0 (Header-Based Import)
