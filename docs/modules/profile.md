## Profile Module

### Purpose
Allow authenticated users to view, update profile info, and delete account.

### Key Files
- Controller: `app/Http/Controllers/ProfileController.php`
- Request: `app/Http/Requests/ProfileUpdateRequest.php`
- Pages: `resources/js/Pages/Profile/Edit.vue`
- Routes: `/profile` under authenticated group

### Behaviors
- Edit: shows form with email verification state
- Update: validates name and unique lowercase email, clears `email_verified_at` on email change
- Destroy: validates current password, logs out, deletes user, clears session

### Notes
- Consider adding profile avatar and additional fields via separate requests and policies