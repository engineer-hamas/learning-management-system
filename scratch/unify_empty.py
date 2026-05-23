import glob
import re

php_files = glob.glob('src/Views/**/*.php', recursive=True)

for file in php_files:
    with open(file, 'r') as f:
        content = f.read()

    # Replace variations of empty states with a single unified class
    content = re.sub(r'class="empty-content"', 'class="empty-state"', content)
    content = re.sub(r'class="empty-state-dashed"', 'class="empty-state"', content)
    content = re.sub(r'class="empty-state-card"', 'class="empty-state"', content)
    content = re.sub(r'class="empty-enrolled-box"', 'class="empty-state"', content)

    with open(file, 'w') as f:
        f.write(content)

print("Empty states consolidated in PHP.")
