---
name: Controllers and Logic Skill
description: Design principles for namespace Controllers under App\Controllers, extending base class, validation, and session states.
---

# Controllers and Logic Skill

This skill defines the rules for implementing application logic inside controllers. In our architecture, controllers act as the bridge between model data, business flow, and response views.

## Subskills Navigation

Use the specific subskills based on your logical operations:

### 1. [Extending the Base Controller](file:///home/hamas/projects/php-project/.agents/skills/controllers-and-logic/controller-base.md)
* **Condition**: When creating a new controller or adding actions.
* **Topic**: Namespace patterns, inheriting from `App\Core\Controller`, and processing HTTP request methods.

### 2. [Secure Sessions and Authentication Flows](file:///home/hamas/projects/php-project/.agents/skills/controllers-and-logic/session-and-auth.md)
* **Condition**: If you are coding user login, register, session persistence, logout, or roles verification.
* **Topic**: Setting and checking auth states, destroying sessions securely, regenerating session IDs to avoid session fixation.

### 3. [Input Validation Spec](file:///home/hamas/projects/php-project/.agents/skills/controllers-and-logic/validation.md)
* **Condition**: When processing POST forms, evaluating fields, verifying emails, or handling empty values.
* **Topic**: Safe input collection, filter verification, and loading validation error states into the session view.
