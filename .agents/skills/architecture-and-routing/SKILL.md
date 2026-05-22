---
name: Architecture and Routing Skill
description: Core MVC engine, including lightweight PSR-4 autoloading, OO programmatic routing, and request/response wrappers.
---

# Architecture and Routing Skill

This skill governs the base core components of the application. The project relies on a custom-designed, extremely fast, OOP core architecture rather than legacy manual files or a bloated third-party framework.

## Subskills Navigation

Depending on what core mechanism you are working with or extending, refer to the corresponding subskill file:

### 1. [PSR-4 Autoloading](file:///home/hamas/projects/php-project/.agents/skills/architecture-and-routing/autoloading.md)
* **Condition**: If you are introducing a new class, adding namespaces, or experiencing class-not-found errors.
* **Topic**: How class loading works dynamically without Composer, map custom namespace prefixes (`App\`) to files.

### 2. [Programmatic OOP Routing](file:///home/hamas/projects/php-project/.agents/skills/architecture-and-routing/routing.md)
* **Condition**: If you are adding a new page, changing a URL path, handling custom requests, or implementing redirects.
* **Topic**: Registering HTTP endpoints (GET/POST), executing matching controller actions, and dispatching.

### 3. [HTTP Request/Response Abstraction](file:///home/hamas/projects/php-project/.agents/skills/architecture-and-routing/http-abstraction.md)
* **Condition**: If you are accessing query parameters, parsing POST request data, setting headers, outputting JSON, or rendering views.
* **Topic**: Interacting with headers, query items, parameters, body payloads, rendering layout HTML, and JSON outputs.
