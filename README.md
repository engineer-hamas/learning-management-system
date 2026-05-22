# Basic PHP Project

A simple, well-organized starting point for vanilla PHP applications without relying on a full-fledged framework.

## 📁 Project Architecture

This project follows a clean directory structure separating public-facing assets from core logic.

```text
php-project/
├── public/               # 🌐 Document Root (Point your web server here)
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── index.php         # Entry point for the application
├── config/               # ⚙️ Configuration files (database, app settings)
│   └── config.php
├── includes/             # 🛠️ Helper functions and procedural scripts
│   └── functions.php
├── src/                  # 🧠 Core application logic
│   ├── Controllers/      # Request handlers
│   ├── Models/           # Database interaction and business logic
│   └── Views/            # HTML templates
├── .gitignore            # Git ignored files
└── README.md             # Project documentation
```

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- Composer (optional, for dependency management)
- MySQL/MariaDB (optional, if you plan to use a database)

### Installation

1. **Clone the repository or create a new folder:**
   ```bash
   git clone <your-repo-url> my-project
   cd my-project
   ```

2. **Install dependencies with Bun:**
   We use Bun as our package manager to handle frontend tooling (like SASS).
   ```bash
   bun install
   ```

3. **Compile SASS to CSS:**
   We have setup a basic SCSS architecture in `src/scss/`. To build the CSS to `public/css/style.css`, run:
   ```bash
   bun run build:css
   ```
   *Tip: During development, you can use `bun run watch:css` in a separate terminal to automatically recompile your styles on every save.*

4. **Start the local development server:**
   PHP has a built-in web server perfect for local development. You can run this command directly or use the bun script:
   ```bash
   bun run dev
   # Which runs: php -S localhost:8000 -t public/
   ```

5. **View the application:**
   Open your browser and navigate to: [http://localhost:8000](http://localhost:8000)

## 💡 Next Steps

1. **Database:** Open `config/config.php` to set up your database credentials.
2. **Styles:** Modify the SCSS files in `src/scss/` and watch your changes reflect live.
3. **Logic:** Start writing your application logic inside the `src/` directory.
4. **Routing:** If you need multiple pages, consider adding a basic router in `public/index.php`.
