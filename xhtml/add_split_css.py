import re

css_file = r"c:\xampp\htdocs\Nishchal\xhtml\css\style.css"
with open(css_file, "r", encoding="utf-8") as f:
    css = f.read()

split_css = """
        /* ─── SPLIT PANEL ─── */
        #new-courses-wrapper .prog-panel.split-vertical {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            background: var(--border);
        }
        #new-courses-wrapper .prog-panel.split-vertical img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
"""
resp_start = css.find("/* ─── RESPONSIVE ─── */")
if resp_start != -1:
    css = css[:resp_start] + split_css + css[resp_start:]

with open(css_file, "w", encoding="utf-8") as f:
    f.write(css)

print("CSS added.")
