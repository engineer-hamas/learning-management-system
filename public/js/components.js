// Generic UI components scripts (Accordions, Dropdowns, Modals)

document.addEventListener('DOMContentLoaded', () => {
    // Modal Close on overlay click
    document.querySelectorAll('.modal').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.searchable-dropdown').forEach((dropdown, idx) => {
            if (!dropdown.contains(e.target)) {
                const list = dropdown.querySelector('[id^="dropdown-list-"]');
                const input = dropdown.querySelector('[id^="search-input-"]');
                if (list) list.style.display = 'none';
                if (input) input.style.borderColor = '#cbd5e1';
            }
        });
    });
});

// Accordion functionality
function toggleAccordion(contentId, iconId) {
    const content = document.getElementById(contentId);
    const icon = document.getElementById(iconId);

    if (content.classList.contains('accordion__content--active')) {
        content.classList.remove('accordion__content--active');
        icon.classList.remove('accordion__content--active');
    } else {
        content.classList.add('accordion__content--active');
        icon.classList.add('accordion__content--active');
        content.style.overflow = "visible";
    }
}

// Modal functionality
function openModal(id) {
    document.getElementById(id).classList.add('accordion__content--active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('accordion__content--active');
}

// Searchable Dropdown Logic
function openDropdown(index) {
    document.getElementById('dropdown-list-' + index).style.display = 'block';
    document.getElementById('search-input-' + index).style.borderColor = '#3b82f6';
}

function filterDropdown(index) {
    const input = document.getElementById('search-input-' + index);
    const filter = input.value.toLowerCase();
    const list = document.getElementById('dropdown-list-' + index);
    const items = list.querySelectorAll('.dropdown-item');
    let hasVisible = false;

    items.forEach(item => {
        const text = item.getAttribute('data-search');
        if (text.includes(filter)) {
            item.style.display = 'block';
            hasVisible = true;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('no-results-' + index).style.display = hasVisible ? 'none' : 'block';
}

function selectOption(index, id, label) {
    document.getElementById('search-input-' + index).value = label;
    document.getElementById('hidden-input-' + index).value = id;
    document.getElementById('dropdown-list-' + index).style.display = 'none';
    document.getElementById('search-input-' + index).style.borderColor = '#cbd5e1';
}
