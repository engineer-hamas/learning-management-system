import re

def rewrite(path, rules):
    with open(path, 'r') as f:
        content = f.read()
    for old, new in rules.items():
        content = content.replace(old, new)
    with open(path, 'w') as f:
        f.write(content)

scss_rules = {
    '.accordion-item {': '.accordion__item {',
    '.accordion-item:hover {': '.accordion__item:hover {',
    '.accordion-header {': '.accordion__header {',
    '.accordion-header:hover {': '.accordion__header:hover {',
    '.accordion-header h3 {': '.accordion__header h3 {',
    '.accordion-content {': '.accordion__content {',
    '.accordion-content.active {': '.accordion__content--active {',
    '.chevron {': '.accordion__icon {',
    '.chevron.active {': '.accordion__icon--active {',
    
    '.item-details {': '.accordion-detail {',
    '.item-details:hover {': '.accordion-detail:hover {',
    '.item-summary {': '.accordion-detail__summary {',
    '.item-summary::-webkit-details-marker {': '.accordion-detail__summary::-webkit-details-marker {',
    '.item-summary:hover {': '.accordion-detail__summary:hover {',
    '.item-body {': '.accordion-detail__body {',
    
    '.modal-overlay {': '.modal {',
    '.modal-overlay.active {': '.modal--active {',
    '.modal-content {': '.modal__content {',
    '.modal-overlay.active .modal-content {': '.modal--active .modal__content {',
    '.modal-header {': '.modal__header {',
    '.modal-close {': '.modal__close {',
    '.modal-close:hover {': '.modal__close:hover {',
    
    '.student-card {': '.student-card {',
    '.student-card::before {': '.student-card::before {',
    '.student-card:hover {': '.student-card:hover {',
    '.student-card:hover::before {': '.student-card:hover::before {',
    '.enter-course-btn {': '.student-card__btn {',
    '.student-card:hover .enter-course-btn {': '.student-card:hover .student-card__btn {',
    
    '.alert-danger-box {': '.alert--danger {',
    '.alert-success-box {': '.alert--success {',
}

rewrite('src/scss/_components.scss', scss_rules)
rewrite('src/scss/_enrollments.scss', scss_rules)

print("BEM classes applied to SCSS")
