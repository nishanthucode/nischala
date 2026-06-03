import re

with open('admin/index.html', 'r', encoding='utf-8') as f:
    html = f.read()

# Remove the 4 dummy blog cards
html = re.sub(r'<div class="col-xl-3 col-xxl-4 col-lg-6 col-md-6">\s*<div class="card">\s*<img class="img-fluid" src="images/courses/pic[1-4]\.jpg".*?</div>\s*</div>\s*</div>', '', html, flags=re.DOTALL)

# Remove the email compose form
html = re.sub(r'<div class="col-xl-6 col-xxl-6 col-lg-6 col-md-12 col-sm-12">\s*<div class="card">\s*<div class="card-body">\s*<form action="#" method="post">.*?</form>\s*</div>\s*</div>\s*</div>', '', html, flags=re.DOTALL)

with open('admin/index.html', 'w', encoding='utf-8') as f:
    f.write(html)
