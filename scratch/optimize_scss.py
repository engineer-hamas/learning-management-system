import re
import glob

def optimize_scss(file_path):
    with open(file_path, 'r') as f:
        content = f.read()

    # Replace flex-between
    content = re.sub(
        r'display:\s*flex;\s*justify-content:\s*space-between;\s*align-items:\s*center;',
        '@include flex-between;',
        content,
        flags=re.MULTILINE
    )
    content = re.sub(
        r'display:\s*flex;\s*align-items:\s*center;\s*justify-content:\s*space-between;',
        '@include flex-between;',
        content,
        flags=re.MULTILINE
    )

    # Replace flex-center
    content = re.sub(
        r'display:\s*flex;\s*justify-content:\s*center;\s*align-items:\s*center;',
        '@include flex-center;',
        content,
        flags=re.MULTILINE
    )
    content = re.sub(
        r'display:\s*flex;\s*align-items:\s*center;\s*justify-content:\s*center;',
        '@include flex-center;',
        content,
        flags=re.MULTILINE
    )

    # Replace flex-gap
    content = re.sub(
        r'display:\s*flex;\s*align-items:\s*center;\s*gap:\s*([^;]+);',
        r'@include flex-gap(\1);',
        content,
        flags=re.MULTILINE
    )

    # Convert simple .class:hover { ... } into nesting if it immediately follows .class { ... }
    # This is slightly complex to do safely with regex, so we'll skip aggressive automated nesting
    # and focus on mixin replacements and manual nesting where it matters most.

    # Remove empty lines that might have been created
    content = re.sub(r'\n\s*\n', '\n\n', content)

    with open(file_path, 'w') as f:
        f.write(content)

scss_files = glob.glob('src/scss/*.scss')
for file in scss_files:
    if file != 'src/scss/_mixins.scss':
        optimize_scss(file)

print("SCSS files optimized with mixins.")
