---
name: Views and Frontend Skill
description: Core specifications for HTML view templates, CSS styling standards (HSL tailormade styles, glassmorphism), and security escaping.
---

# Views and Frontend Skill

This skill defines the guidelines and styling expectations for the presentation layer. The user experience is of absolute importance, and all interfaces must display premium visual design.

## Subskills Navigation

Consult the specific frontend subskills based on your task parameters:

### 1. [View Templates & Rendering](file:///home/hamas/projects/php-project/.agents/skills/views-and-frontend/view-rendering.md)
* **Condition**: When creating an HTML page, editing sections, adding page forms, or rendering parameters.
* **Topic**: View scope execution, passing arrays of data from controllers, and layout rules.

### 2. [Premium Aesthetic & Styling Standards](file:///home/hamas/projects/php-project/.agents/skills/views-and-frontend/styling-standards.md)
* **Condition**: When writing or updating stylesheets (`src/scss/style.scss`), changing colors, borders, fonts, or animations.
* **Topic**: Advanced HSL color systems, glassmorphism, card shadows, responsive mobile views, and Google Fonts (`Outfit` font family).

### 3. [Frontend Security (XSS Escaping)](file:///home/hamas/projects/php-project/.agents/skills/views-and-frontend/security.md)
* **Condition**: Whenever rendering any dynamic variable or user record inside HTML views (e.g. usernames, roles).
* **Topic**: Secure `e()` output helper, HTML sanitization, and avoiding cross-site scripting.
