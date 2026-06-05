import os
import re

directory = r'c:\xampp\htdocs\Nishchal\xhtml\admin'

for filename in os.listdir(directory):
    if filename.endswith('.html'):
        filepath = os.path.join(directory, filename)
        with open(filepath, 'r', encoding='utf-8') as file:
            content = file.read()
        
        # Replace Title
        content = re.sub(r'<title>.*?</title>', '<title>Nishchala | Classical Hatha Yoga in its Purest Form</title>', content, flags=re.IGNORECASE)
        
        # Replace Favicon
        content = re.sub(r'<link rel="icon"[^>]*href="images/favicon[^"]*\.png"[^>]*>', '<link rel="icon" type="image/png" sizes="16x16" href="images/logo.png">', content, flags=re.IGNORECASE)
        content = re.sub(r'<link rel="shortcut icon"[^>]*href="images/favicon[^"]*\.png"[^>]*>', '<link rel="shortcut icon" type="image/png" href="images/logo.png">', content, flags=re.IGNORECASE)

        with open(filepath, 'w', encoding='utf-8') as file:
            file.write(content)

print('Updated all HTML files successfully.')
