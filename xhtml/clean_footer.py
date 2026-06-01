import os
import re

directory = r'd:\OPT\education-course-school-template-admin-dashboard-2026-01-09-11-28-25-utc\Eduzone-v2.4.2-19_April_2025\BS-4\BS-4\xhtml'
files = ['about-1.html', 'blog-grid-4.html', 'blog-list.html', 'contact-4.html', 'courses.html', 'gallery-grid-4.html', 'index.html', 'services-1.html']

old_social = """<ul style="margin: 0; padding: 0;">
								<li style="display: inline-block; margin: 0 2px;"><a class="site-button sharp-sm fa fa-facebook" style="background-color: #f77024; color: white; border-radius: 50%; border: none;" href="javascript:void(0);"></a></li>
								<li style="display: inline-block; margin: 0 2px;"><a class="site-button sharp-sm fa fa-twitter" style="background-color: #f77024; color: white; border-radius: 50%; border: none;" href="javascript:void(0);"></a></li>
								<li style="display: inline-block; margin: 0 2px;"><a class="site-button sharp-sm fa fa-linkedin" style="background-color: #f77024; color: white; border-radius: 50%; border: none;" href="javascript:void(0);"></a></li>
								<li style="display: inline-block; margin: 0 2px;"><a class="site-button sharp-sm fa fa-instagram" style="background-color: #f77024; color: white; border-radius: 50%; border: none;" href="javascript:void(0);"></a></li>
							</ul>"""

new_social = """<ul>
								<li><a class="site-button sharp-sm fa fa-facebook" href="javascript:void(0);"></a></li>
								<li><a class="site-button sharp-sm fa fa-twitter" href="javascript:void(0);"></a></li>
								<li><a class="site-button sharp-sm fa fa-linkedin" href="javascript:void(0);"></a></li>
								<li><a class="site-button sharp-sm fa fa-instagram" href="javascript:void(0);"></a></li>
							</ul>"""

for f in files:
    filepath = os.path.join(directory, f)
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as file:
            content = file.read()
        
        if old_social in content:
            new_content = content.replace(old_social, new_social)
            with open(filepath, 'w', encoding='utf-8') as file:
                file.write(new_content)
            print(f"Cleaned {f}")
        else:
            # Let's try replacing them individually if the formatting is different
            new_content = content.replace('style="margin: 0; padding: 0;"', '')
            new_content = new_content.replace('style="display: inline-block; margin: 0 2px;"', '')
            new_content = new_content.replace('style="background-color: #f77024; color: white; border-radius: 50%; border: none;"', '')
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f"Cleaned individually {f}")
            else:
                print(f"Skipped {f}")
    else:
        print(f"File not found: {f}")
