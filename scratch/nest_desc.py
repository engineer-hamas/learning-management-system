import re
import glob

def collapse_descendant(file_path):
    with open(file_path, 'r') as f:
        content = f.read()

    # Find patterns like `.parent .child { ... }` and nest `.child { ... }` into `.parent { ... }`
    # E.g. `.student-card:hover .student-card__btn` into `& .student-card__btn` inside `.student-card:hover`
    
    replacements = [
        (r'\.accordion__header h3 \{([^}]*)\}', r'', r'\.accordion__header \{', r'\.accordion__header {\n  h3 {\1}'),
        (r'\.modal--active \.modal__content \{([^}]*)\}', r'', r'\.modal--active \{', r'\.modal--active {\n  .modal__content {\1}'),
        (r'\.student-card:hover \.student-card__btn \{([^}]*)\}', r'', r'\.student-card \{\n  &:hover \{', r'\.student-card {\n  &:hover {\n    .student-card__btn {\1}'),
        (r'\.student-card:hover::before \{([^}]*)\}', r'', r'\.student-card \{\n  &:hover \{', r'\.student-card {\n  &:hover {\n    &::before {\1}'),
        (r'\.student-card::before \{([^}]*)\}', r'', r'\.student-card \{', r'\.student-card {\n  &::before {\1}')
    ]

    for desc_pattern, remove_replacement, target_pattern, nest_replacement in replacements:
        match = re.search(desc_pattern, content)
        if match:
            content = re.sub(desc_pattern, remove_replacement, content)
            content = re.sub(target_pattern, nest_replacement.replace(r'\1', match.group(1)), content)

    content = re.sub(r'\n\s*\n', '\n\n', content)

    with open(file_path, 'w') as f:
        f.write(content)

for file in glob.glob('src/scss/*.scss'):
    collapse_descendant(file)

print("Descendant states collapsed.")
