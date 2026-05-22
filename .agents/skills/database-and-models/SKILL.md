---
name: Database and Models Skill
description: Database architecture rules, including object-oriented wrappers, auto-schema generation, and secure SQL writing.
---

# Database and Models Skill

This skill defines the rules for database integration and model structures. To protect user records and guarantee speed, all database interactions must use object-oriented prepared interfaces.

## Subskills Navigation

Consult the specific database subskills depending on your operations:

### 1. [Encapsulated Connection Wrapper](file:///home/hamas/projects/php-project/.agents/skills/database-and-models/database-connection.md)
* **Condition**: If you are changing credentials, altering connection details, or modifying the initialization schema.
* **Topic**: Singleton connection management, database auto-creation, and schema creation policies.

### 2. [Prepared SQL Statement Specification](file:///home/hamas/projects/php-project/.agents/skills/database-and-models/prepared-statements.md)
* **Condition**: When writing database queries, checking records, executing insertions, or running updates.
* **Topic**: Parameterized query structures, preventing SQL injection, binding types, and fetching results securely.
