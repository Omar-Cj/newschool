
## Improvements for Main Dashboard: School Edit & Super Admin Management

### Background
- The main dashboard (`Modules/MainApp`) handles school creation.
- When creating a school, we also register the super admin user for that school.

### Needed Improvements

1. **Edit Super Admin with School Edit**
   - When editing a school, provide the ability to edit **the associated Super Admin user’s** we created:
     - Email
     - Password

2. **User Interface Updates**
   - In the "Edit School" modal or page, add fields for:
     - Super Admin Email (pre-filled with current email)
     - New Password (optional, leave blank to keep unchanged)

3. **Backend/Logic Changes**
   - When updating a school:
     - Validate the new email for uniqueness.
     - If a new password is provided, securely hash and update it.
     - Update the super admin’s email if it was changed.

4. **Security**
   - Use strong validation for email and password.
   - Ensure only authorized administrators can edit super admin credentials.



**Summary:**  
Allow editing and updating the super admin's email and password directly from the school edit functionality to simplify management and improve admin UX.



## Improvements for School-Specific Dashboard: Staff Password Updates

### Background
- Each school has a **Super Admin** user (role_id = 1).
- There are staff users connected to different branches within a school.
- Regular **Admin** users may have permissions only for their specific branch.

### Needed Improvements

1. **Password Reset by Super Admins (role_id = 1)**
   - In the school-specific dashboard, **Super Admins** should be able to edit any staff user’s password, regardless of branch.
   - When editing a staff user, include a “New Password” field (optional, blank means no change).
   - Ensure password is securely hashed on update.

2. **Branch-based Password Reset by Regular Admins**
   - Regular Admin users (not Super Admin, but admins for a specific branch) should:
     - edit only their branch’s staff.
     - Have access to the “New Password” field when editing staff in their branch.
     

3. **User Interface Updates**
   - On the staff edit modal/page:
     - Display a “New Password” field for authorized admins (as above).
     - Field is optional; if left blank, keep password unchanged.

4. **Backend/Logic Changes**
   - When saving staff edits:
     - If “New Password” is provided, validate and securely hash before saving.
     - Verify the editing user has permission for the targeted staff/user (Super Admin = all; Branch Admin = own branch only).
     - Log password changes for audit purposes.

5. **Security**
   - Validate new passwords for strength.
   - Restrict feature access to only qualified admins as outlined.
   - Safeguard logging so no sensitive data (actual passwords) are stored.

**Summary:**  
Enable secure staff password updates from the school dashboard by authorized Super Admins (all branches) or Admins (own branch), improving local user management and overall account security.


