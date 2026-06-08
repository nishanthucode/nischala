with open(r'c:\xampp\htdocs\Nishchal\xhtml\courses.html', 'r', encoding='utf-8') as f:
    lines = f.readlines()

start = -1
end = -1
for i, line in enumerate(lines):
    if 'id="new-courses-wrapper"' in line:
        start = i
    if '<!-- Footer -->' in line or 'class="site-footer"' in line:
        end = i
        break

print(f'Start: {start}, End: {end}')
