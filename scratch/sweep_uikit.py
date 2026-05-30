import glob
import re

php_files = glob.glob('src/Views/**/*.php', recursive=True)

for file in php_files:
    with open(file, 'r') as f:
        content = f.read()

    # Buttons
    content = re.sub(r'class="add-btn"', 'class="btn btn--primary"', content)
    content = re.sub(r'class="enter-course-btn"', 'class="btn btn--primary"', content)
    content = re.sub(r'class="file-download-btn"', 'class="btn btn--ghost"', content)
    content = re.sub(r'class="btn-primary"', 'class="btn btn--primary"', content)

    # Typography
    content = re.sub(r'class="course-banner__title[^"]*"', 'class="text-title"', content)
    content = re.sub(r'class="course-banner__subtitle[^"]*"', 'class="text-subtitle text-muted"', content)

    # Headers
    content = re.sub(r'class="accordion__header"', 'class="section-header"', content)
    content = re.sub(r'<span class="accordion__icon"', '<span class="btn btn--icon"', content)
    
    # Cards
    content = re.sub(r'class="accordion__item"', 'class="card card--glass"', content)

    with open(file, 'w') as f:
        f.write(content)

print("Swept PHP Views with new UI kit classes.")
