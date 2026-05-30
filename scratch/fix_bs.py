import re
import glob

for file in glob.glob('src/scss/*.scss'):
    with open(file, 'r') as f:
        content = f.read()

    # Replace `\.` with `.`
    content = content.replace(r'\.', '.')
    
    with open(file, 'w') as f:
        f.write(content)

print("Fixed backslashes in SCSS")
