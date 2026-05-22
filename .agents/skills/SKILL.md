---
name: PHP Project Modular Architecture & Consistency Skills
description: Central entry point and router for the PHP LMS project's nested architectural, coding standard, database, and presentation skills.
---

# PHP Modular Architecture & Consistency Skills

Welcome! This is the central repository of development skills and engineering standards for this PHP application. To ensure absolute consistency and architectural purity, this skills framework is divided into modular, conditions-driven subskills. 

## Skills Directory Router

Read the corresponding `SKILL.md` file based on the type of task you are performing:

| Condition / Task Type | Recommended Skill Folder | Description |
| :--- | :--- | :--- |
| **All Tasks (MANDATORY)** | [project-consistency-manifest/](file:///home/hamas/projects/php-project/.agents/skills/project-consistency-manifest/SKILL.md) | Enforces naming conventions, file paths, casing rules, and structure across the entire workspace. |
| **Routing, Autoloading, Core Engine** | [architecture-and-routing/](file:///home/hamas/projects/php-project/.agents/skills/architecture-and-routing/SKILL.md) | Custom PSR-4 autoloading, programmatic OOP routing, and HTTP Request/Response wrapping. |
| **Adding/Modifying Controllers, Auth, Forms** | [controllers-and-logic/](file:///home/hamas/projects/php-project/.agents/skills/controllers-and-logic/SKILL.md) | Base controller extension, input validation, and secure session management/Authentication. |
| **Database Queries, Migrations, Table Schemas** | [database-and-models/](file:///home/hamas/projects/php-project/.agents/skills/database-and-models/SKILL.md) | Encapsulated DB connection class, MySQLi prepared statements, and auto-initialization. |
| **Views, Styling, CSS/SCSS, HTML, XSS escaping** | [views-and-frontend/](file:///home/hamas/projects/php-project/.agents/skills/views-and-frontend/SKILL.md) | View rendering, HSL design tokens, Glassmorphism aesthetics, and secure XSS escaping. |

## Quick Architectural Overview

This project uses a clean **Vanilla MVC architecture** structured as follows:

```
├── .agents/skills/              # Hierarchical developer skills & specs
├── config/                      # Global environment and app config parameters
├── public/                      # Web root directory (index.php front-controller, assets)
│   ├── css/                     # Compiled premium style files
│   ├── js/                      # Frontend script files
│   └── index.php                # Front controller and router entry
├── src/                         # Source directory
│   ├── Core/                    # Base OO MVC engine (Router, Autoloader, Req, Res, DB)
│   ├── Controllers/             # Namespaced controllers (App\Controllers)
│   ├── Models/                  # Namespaced models (App\Models)
│   ├── Views/                   # View templates loaded via Core\Response
│   └── scss/                    # Advanced styling SCSS source files
└── package.json                 # Build, watch, and lint scripts
```

## How to use these skills
Before modifying any files in this project:
1. Identify the task category (e.g. changing database columns, adding views, updating controllers).
2. Load and read the corresponding `SKILL.md` inside `.agents/skills/`.
3. Follow the specific instructions and examples carefully. Maintain 100% compliance with conventions.
