#!/usr/bin/env python3
import os
import glob

# Define the CSS files to update
css_files = [
    r'd:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\admin\css\style.css',
    r'd:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\admin\css\skin.css',
    r'd:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\admin\css\skin-2.css',
    r'd:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\admin\css\skin-3.css'
]

print("=" * 60)
print("SIDEBAR WIDTH UPDATE TOOL")
print("=" * 60)
print()

total_replacements = 0

for file_path in css_files:
    filename = os.path.basename(file_path)
    if os.path.exists(file_path):
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Count occurrences before
        before_count = content.count('17.1875rem')
        
        if before_count > 0:
            # Replace the value
            new_content = content.replace('17.1875rem', '22rem')
            
            # Write back to file
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            
            print(f"✓ {filename}")
            print(f"  Replaced: {before_count} occurrences")
            total_replacements += before_count
        else:
            print(f"- {filename}")
            print(f"  No changes needed (0 occurrences)")
    else:
        print(f"✗ {filename}")
        print(f"  File not found!")
    print()

print("=" * 60)
print(f"SUMMARY: Total replacements made: {total_replacements}")
print("=" * 60)

# Verify the changes
print()
print("VERIFICATION:")
print()
for file_path in css_files:
    filename = os.path.basename(file_path)
    if os.path.exists(file_path):
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        new_count = content.count('22rem')
        print(f"{filename}: {new_count} instances of '22rem'")
