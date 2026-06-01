# Backend setup for the existing template

This backend is intentionally separated from the visual template so the design stays unchanged.

## 1. Create the database

1. Open phpMyAdmin from XAMPP.
2. Import `backend/schema.sql`.
3. Update `backend/config.php` if your MySQL username, password, or database name is different.

## 2. Make the template pages PHP-aware

The file `xhtml/.htaccess` enables PHP execution inside `.html` and `.htm` files in this folder.

If Apache on your XAMPP setup does not allow this handler, the fallback is to rename only the pages you want dynamic to `.php`.

## 3. Backend files already added

- `backend/config.php` for the PDO connection.
- `backend/modules.php` for the CRUD field definitions.
- `backend/functions.php` for fetch, save, delete, and upload helpers.
- `backend/save.php` for insert/update requests.
- `backend/delete.php` for delete requests.
- `backend/list.php` for JSON output.

## 4. Tables covered

- `classes`
- `events`
- `gallery`
- `blogs`

## 5. How to connect the existing admin forms

For each admin add/edit page, change the form to submit to `../backend/save.php` and include a hidden `module` field.

Example:

```html
<form action="../backend/save.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="module" value="blogs">
  <input type="hidden" name="id" value="">
</form>
```

Then add `name` attributes that match the module fields in `backend/modules.php`.

## 6. How to connect the list pages

For each `all-*.html` page, query the matching table with `backend_fetch_all()` or call `backend/list.php?module=blogs` from JavaScript.

## 7. Suggested page mapping

- `add-courses.html` and `edit-courses.html` -> `classes`
- `event-management.html` and a custom event form page -> `events`
- `add-gallery.html`, `edit-gallery.html`, `all-gallery.html` -> `gallery`
- `add-blog.html`, `edit-blog.html`, `all-blogs.html` -> `blogs`

## 8. Upload folder

Uploaded files are stored in `uploads/<module>/`.
