import re

def rewrite(path, rules):
    with open(path, 'r') as f:
        content = f.read()
    for old, new in rules.items():
        content = content.replace(old, new)
    with open(path, 'w') as f:
        f.write(content)

php_rules = {
    'class="accordion-item"': 'class="accordion__item"',
    'class="accordion-header"': 'class="accordion__header"',
    'class="accordion-content"': 'class="accordion__content"',
    'class="accordion-content active"': 'class="accordion__content accordion__content--active"',
    'class="chevron"': 'class="accordion__icon"',
    'class="chevron active"': 'class="accordion__icon accordion__icon--active"',
    
    'class="item-details"': 'class="accordion-detail"',
    'class="item-summary"': 'class="accordion-detail__summary"',
    'class="item-body"': 'class="accordion-detail__body"',
    
    'class="modal-overlay"': 'class="modal"',
    'class="modal-overlay active"': 'class="modal modal--active"',
    'class="modal-content"': 'class="modal__content"',
    'class="modal-header"': 'class="modal__header"',
    'class="modal-close"': 'class="modal__close"',
    
    'class="student-card"': 'class="student-card"',
    'class="enter-course-btn"': 'class="student-card__btn"',
    
    'class="alert-danger-box"': 'class="alert alert--danger"',
    'class="alert-success-box"': 'class="alert alert--success"',
}

js_rules = {
    "'accordion-content'": "'accordion__content'",
    "'.accordion-content'": "'.accordion__content'",
    "'active'": "'accordion__content--active'",
    "'chevron'": "'accordion__icon'",
    "'.chevron'": "'.accordion__icon'",
    "'.modal-overlay'": "'.modal'",
    "'.modal-overlay.active'": "'.modal.modal--active'",
    "'modal-overlay'": "'modal'",
}

rewrite('src/Views/components/common/accordion-section.php', php_rules)
rewrite('src/Views/course.php', php_rules)
rewrite('public/js/components.js', js_rules)
rewrite('src/Views/enrollments.php', php_rules)

print("BEM classes applied to HTML and JS")
