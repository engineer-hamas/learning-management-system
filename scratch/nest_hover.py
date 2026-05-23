import re
import glob

def collapse_hover(file_path):
    with open(file_path, 'r') as f:
        content = f.read()

    # We want to find `.class:hover { ... }` that comes shortly after `.class { ... }`
    # But doing this safely with regex is tricky. 
    # Instead, let's just find `.class:hover` and `.class::before` and `.class.active` 
    # anywhere in the file and wrap them inside the main `.class { ... }`.
    # Since writing a full SCSS parser in Python is hard, let's target the exact ones we know:
    
    replacements = [
        (r'\.accordion__item:hover \{([^}]*)\}', r'', r'\.accordion__item \{', r'\.accordion__item {\n  &:hover {\1}'),
        (r'\.accordion__header:hover \{([^}]*)\}', r'', r'\.accordion__header \{', r'\.accordion__header {\n  &:hover {\1}'),
        (r'\.accordion-detail:hover \{([^}]*)\}', r'', r'\.accordion-detail \{', r'\.accordion-detail {\n  &:hover {\1}'),
        (r'\.accordion-detail__summary:hover \{([^}]*)\}', r'', r'\.accordion-detail__summary \{', r'\.accordion-detail__summary {\n  &:hover {\1}'),
        (r'\.file-download-btn:hover \{([^}]*)\}', r'', r'\.file-download-btn \{', r'\.file-download-btn {\n  &:hover {\1}'),
        (r'\.modal__close:hover \{([^}]*)\}', r'', r'\.modal__close \{', r'\.modal__close {\n  &:hover {\1}'),
        (r'\.student-card:hover \{([^}]*)\}', r'', r'\.student-card \{', r'\.student-card {\n  &:hover {\1}'),
        (r'\.btn-manage-course:hover \{([^}]*)\}', r'', r'\.btn-manage-course \{', r'\.btn-manage-course {\n  &:hover {\1}'),
        (r'\.searchable-input:focus \{([^}]*)\}', r'', r'\.searchable-input \{', r'\.searchable-input {\n  &:focus {\1}'),
        (r'\.dropdown-item-styled:hover \{([^}]*)\}', r'', r'\.dropdown-item-styled \{', r'\.dropdown-item-styled {\n  &:hover {\1}'),
        (r'\.btn-enroll-submit:hover \{([^}]*)\}', r'', r'\.btn-enroll-submit \{', r'\.btn-enroll-submit {\n  &:hover {\1}'),
        (r'\.table-body-row:hover \{([^}]*)\}', r'', r'\.table-body-row \{', r'\.table-body-row {\n  &:hover {\1}'),
        (r'\.btn-remove-enrollment:hover \{([^}]*)\}', r'', r'\.btn-remove-enrollment \{', r'\.btn-remove-enrollment {\n  &:hover {\1}')
    ]

    for hover_pattern, remove_replacement, target_pattern, nest_replacement in replacements:
        # Extract the hover content
        match = re.search(hover_pattern, content)
        if match:
            # Remove the hover block
            content = re.sub(hover_pattern, remove_replacement, content)
            # Inject it into the parent block
            content = re.sub(target_pattern, nest_replacement.replace(r'\1', match.group(1)), content)

    # Clean up multiple blank lines
    content = re.sub(r'\n\s*\n', '\n\n', content)

    with open(file_path, 'w') as f:
        f.write(content)

for file in glob.glob('src/scss/*.scss'):
    collapse_hover(file)

print("Hover states collapsed.")
