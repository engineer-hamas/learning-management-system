import re
import os

components_file = 'src/scss/_components.scss'
with open(components_file, 'r') as f:
    content = f.read()

# Remove the course-header-banner from _components.scss (we already moved it)
content = re.sub(r'\.course-header-banner.*?\n}\n\n', '', content, flags=re.DOTALL)
content = re.sub(r'\.course-header-banner\.teacher.*?\n}\n\n', '', content, flags=re.DOTALL)
content = re.sub(r'\.course-header-banner\.student.*?\n}\n\n', '', content, flags=re.DOTALL)

with open(components_file, 'w') as f:
    f.write(content)

print("Cleaned up course banner")
