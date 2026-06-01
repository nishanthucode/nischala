#!/usr/bin/env python3
import os
import re

# Define the base directory
base_dir = r"d:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\admin"

# Define the files to update
files_to_update = [
    "index-3.html",
    "layout-blank.html",
    "layout-compact-nav.html",
    "layout-dark.html",
    "layout-fixed-header.html",
    "layout-fixed-nav.html",
    "layout-full-nav.html",
    "layout-light.html",
    "layout-mini-nav.html"
]

# Define the new sidebar content
new_sidebar = """        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Main Menu</li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
							<i class="la la-home"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="index.html">Dashboard 1</a></li>
                            <li><a href="index-2.html">Dashboard 2</a></li>
                            <li><a href="index-3.html">Dashboard 3</a></li>
                        </ul>
                    </li>
					<li><a class="ai-icon" href="event-management.html" aria-expanded="false">
							<i class="la la-calendar"></i>
							<span class="nav-text">Event Management</span>
						</a>
                    </li>
					<li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
							<i class="la la-graduation-cap"></i>
							<span class="nav-text">Classes</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="all-courses.html">All Classes</a></li>
                            <li><a href="add-courses.html">Add Classes</a></li>
                            <li><a href="edit-courses.html">Edit Classes</a></li>
                            
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="la la-image"></i>
                            <span class="nav-text">Gallery</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="all-gallery.html">All Gallery</a></li>
                            <li><a href="add-gallery.html">Add Gallery</a></li>
                            <li><a href="edit-gallery.html">Edit Gallery</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="la la-file-text"></i>
                            <span class="nav-text">Blog</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="all-blogs.html">All Blogs</a></li>
                            <li><a href="add-blog.html">Add Blog</a></li>
                            <li><a href="edit-blog.html"></a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="la la-envelope"></i>
                            <span class="nav-text">Enquiry</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="all-enquiries.html">All Enquiries</a></li>
                            <li><a href="view-enquiry.html">View Enquiry</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
							<i class="la la-users"></i>
							<span class="nav-text">Students</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="all-students.html">All Patients</a></li>
                            <li><a href="add-student.html">Add Patient</a></li>
                            <li><a href="edit-student.html">Edit Patient</a></li>
                            <li><a href="about-student.html">About Student</a></li>
                        </ul>
                    </li>
				</ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->"""

success_count = 0
failure_count = 0
results = []

for file in files_to_update:
    file_path = os.path.join(base_dir, file)
    
    if os.path.exists(file_path):
        try:
            # Read the file content
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Replace the sidebar using regex pattern
            # Match from <!--**********************************\s*Sidebar start to Sidebar end***********************************-->
            pattern = r'<!--\*+\s*Sidebar start\s*\*+-->[\s\S]*?<!--\*+\s*Sidebar end\s*\*+-->'
            
            if re.search(pattern, content):
                # Perform the replacement
                updated_content = re.sub(pattern, new_sidebar, content)
                
                # Write back to file
                with open(file_path, 'w', encoding='utf-8') as f:
                    f.write(updated_content)
                
                success_count += 1
                results.append(f"{file} - UPDATED")
            else:
                failure_count += 1
                results.append(f"{file} - SIDEBAR PATTERN NOT FOUND")
        except Exception as e:
            failure_count += 1
            results.append(f"{file} - ERROR: {str(e)}")
    else:
        failure_count += 1
        results.append(f"{file} - FILE NOT FOUND")

# Display results
print("========================================")
print("Sidebar Update Report")
print("========================================")
for result in results:
    if "UPDATED" in result:
        print(f"✓ {result}")
    else:
        print(f"✗ {result}")
print("========================================")
print(f"Summary: {success_count} files successfully updated, {failure_count} failed")
print("========================================")
