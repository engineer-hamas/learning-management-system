import glob
import re

scss_files = glob.glob('src/scss/*.scss')

for file in scss_files:
    with open(file, 'r') as f:
        content = f.read()

    # The previous regex replacement mistakenly inserted `\.` instead of `.` for class selectors
    # We need to unescape these backslashes at the start of lines
    content = re.sub(r'\\(\.[a-zA-Z0-9_-]+)', r'\1', content)

    # Also fix any modal-overlay references that were missed
    content = content.replace('.modal-overlay.active', '.modal--active')
    content = content.replace('.modal-header', '.modal__header')
    
    with open(file, 'w') as f:
        f.write(content)

print("Fixed broken SCSS selectors.")
