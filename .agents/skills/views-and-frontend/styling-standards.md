# Premium Styling Standards Subskill

This subskill governs styling rules and visual design guidelines. Visual excellence is a critical deliverable. Applications must look premium, modern, cohesive, and fully responsive.

---

## Design Tokens

Always define cohesive design tokens (using CSS custom properties) inside your stylesheets. Avoid using raw colors or standard defaults.

### Curated HSL Color Tokens
Our premium LMS layout uses custom HSL (Hue, Saturation, Lightness) color values to establish a sleek dark/glass theme:

```css
:root {
    /* Main HSL Palettes */
    --hue-primary: 220;
    --hue-accent: 250;
    
    /* Brand Colors */
    --primary: hsl(var(--hue-primary), 85%, 55%);
    --primary-hover: hsl(var(--hue-primary), 90%, 45%);
    --accent: hsl(var(--hue-accent), 80%, 60%);
    
    /* Backgrounds and Cards */
    --bg-dark: hsl(222, 47%, 11%);
    --bg-card: hsla(222, 47%, 15%, 0.7);
    --border-color: hsla(0, 0%, 100%, 0.08);
    
    /* Typography */
    --font-primary: 'Outfit', sans-serif;
    --text-main: hsl(0, 0%, 95%);
    --text-muted: hsl(215, 15%, 65%);
}
```

---

## Visual Design Guidelines

To guarantee that the UI wows users, implement these key design components:

### 1. Typography
- **Mandatory Font**: Use the premium Google Font family **`Outfit`** (`https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap`).
- NEVER use generic sans-serif or system browser fonts without first loading Outfit.

### 2. Glassmorphism Architecture
Cards and UI wrappers must present a modern glass design with subtle translucent backgrounds, border colors, and backdrop blurs:

```css
.glass-card {
    background: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border-color);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    border-radius: 12px;
}
```

### 3. Responsive Mobile Viewports
Design utilizing flexible CSS Grid layouts and Flexbox. Sidebar navigation must collapse or shift to a clean hamburger menu on smaller screens:

```css
@media (max-width: 768px) {
    .dashboard-layout {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        height: auto;
    }
}
```

### 4. Micro-Animations and Interactive Hover States
Add transitions to all interactive items (buttons, inputs, menu links) to ensure the interface feels responsive and organic:

```css
.menu-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.menu-item:hover {
    background: var(--primary);
    color: var(--text-main);
    transform: translateX(4px);
}
```
