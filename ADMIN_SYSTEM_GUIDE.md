# Souvenir Recommendation System - Admin Guide

## Overview

This project implements a comprehensive route and middleware system based on the Varnion Guide, where all registered users become **Root Admins** with full system access and management capabilities.

## 🚀 Key Features

### Root Admin Access
- **All users become Root Admins** upon registration
- Full system access without restrictions
- Can manage users, souvenirs, and system settings
- No company or site associations (cid=null, sid=null)

### Authentication Flow
1. **Public Access**: Welcome page (`/`) - No login required
2. **User Registration**: Users register and immediately become Root Admins
3. **Login with OTP**: Secure authentication with email verification
4. **Full Access**: After login, users can access all features

### Administrative Capabilities

#### 📊 Admin Dashboard (`/admin`)
- System statistics and overview
- Quick action buttons for management
- Recent users and souvenirs
- Real-time data monitoring

#### 👥 User Management (`/admin/users`)
- View all registered users
- Edit user information
- Promote/demote users between roles
- Delete users (with safety checks)
- Search and filter functionality

#### 🎁 Souvenir Management (`/admin/souvenirs`)
- Create, edit, delete souvenirs
- Search and filter by category/price
- View souvenir details
- Manage product inventory

#### ⚙️ System Settings (`/admin/settings`)
- Configure site name and description
- Enable/disable maintenance mode
- Control user registration
- Manage recommendation system access

#### 💻 System Information (`/admin/settings/info`)
- View Laravel and PHP versions
- Check database and cache configuration
- Monitor system status

## 🔐 Security Implementation

### Middleware Protection
- `auth.session` - Session-based authentication
- `admin.only` - Restricts access to admin users only
- `access.control` - Permission-based route access

### Route Organization
```
/public              → Welcome page (no auth)
/auth/*             → Authentication routes (public)
/home/*             → User features (auth required)
/admin/*             → Admin features (admin auth required)
```

### User Model Enhancements
```php
$user->isAdmin();           // Check if user is admin
$user->isRootAdmin();      // Check if user is root admin
$user->makeAdmin();        // Promote to admin
$user->makeUser();         // Demote to user
```

## 🛠️ Technical Implementation

### Database Schema
- **users table**: Added role fields
  - `role` (string): 'user' or 'admin'
  - `is_admin` (boolean): Admin flag
  - `cid` (nullable): Company ID
  - `sid` (nullable): Site ID
  - `authorized_by` (nullable): Authorization reference

### Access Service
Centralized permission checking via `App\Services\AccessService`:
```php
AccessService::isAdmin();           // Check admin status
AccessService::isRootAdmin();      // Check root admin status
AccessService::can('action');      // Check specific permissions
AccessService::isRouteAccessible('route.name'); // Check route access
```

### Controllers Structure
- `Admin\AdminController` - Main admin functions
- `Admin\UserManagementController` - User CRUD operations
- `Admin\SouvenirManagementController` - Souvenir management
- `Admin\SystemSettingsController` - System configuration

## 📱 Navigation

### For Admin Users
1. **Main Application**: `/home` - Souvenir recommendations
2. **Admin Panel**: `/admin` - Administrative interface
3. **Direct Admin Access**: Admin link in navigation bar

### Access Points
- Welcome page: `/`
- Login: `/login`
- Admin Dashboard: `/admin`
- User Management: `/admin/users`
- Souvenir Management: `/admin/souvenirs`
- Settings: `/admin/settings/general`

## 🚦 Getting Started

### 1. Register as Admin
1. Visit `/register`
2. Fill in registration details
3. Account created with Root Admin privileges
4. Login with email/password
5. Verify OTP code sent to email
6. Access admin panel at `/admin`

### 2. Access Admin Panel
After successful login:
- Click "Admin Panel" in navigation (shield icon)
- Or navigate directly to `/admin`
- Full access to all management features

### 3. Manage System
Use admin panel to:
- Add/edit/delete souvenirs
- Manage users and their roles
- Configure system settings
- Monitor system statistics

## 🔒 Security Features

### Authentication
- OTP-based email verification
- Session management with role tracking
- CSRF protection on all forms

### Authorization
- Middleware-based route protection
- Role-based access control
- Prevents unauthorized access

### Data Protection
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templates
- Secure password hashing (bcrypt)

## 📊 Admin Dashboard Features

### Statistics Overview
- Total users count
- Total admins count
- Total souvenirs count
- Total recommendations count

### Quick Actions
- Direct links to management pages
- One-click access to common tasks
- System shortcuts

### Recent Activity
- Latest registered users
- Recently added souvenirs
- Activity monitoring

## 🎯 Usage Examples

### Promote User to Admin
```php
// Via admin interface
Route::post('/admin/users/{user}/promote', [UserManagementController::class, 'promoteToAdmin']);

// Programmatically
$user->makeAdmin();
```

### Check Admin Access
```php
// In controller or view
if (AccessService::isRootAdmin()) {
    // Grant full access
}
```

### Protect Admin Routes
```php
// In routes/web.php
Route::middleware(['auth.session', 'admin.only'])
    ->prefix('admin')
    ->group(function () {
        // Admin routes here
    });
```

## 🐛 Troubleshooting

### Can't Access Admin Panel
1. Ensure you're logged in
2. Check session data: `session('is_admin')`
3. Verify user role in database
4. Clear cache: `php artisan cache:clear`

### User Not Showing as Admin
1. Check user table: `is_admin` field should be `1`
2. Role field should be 'admin'
3. Run: `php artisan migrate:refresh`
4. Re-register the user

### Routes Not Working
1. Clear route cache: `php artisan route:clear`
2. Check middleware registration in `bootstrap/app.php`
3. Verify route names in controllers

## 📝 Development Notes

### Adding New Admin Features
1. Create controller in `Admin/` namespace
2. Add routes with `admin.only` middleware
3. Create views in `resources/views/admin/`
4. Add navigation link in admin layout

### Modifying Access Control
1. Update `AccessService` methods
2. Modify middleware classes
3. Test with different user roles
4. Update documentation

## 🚀 Future Enhancements

- Audit logging for admin actions
- Multi-factor authentication
- Permission-based fine-grained access
- API endpoints for admin operations
- Real-time notifications
- Advanced reporting and analytics

---

**Version**: 1.0
**Based On**: Varnion Routes and Middleware Guide
**Status**: ✅ Fully Implemented and Functional
