# Mzansi Marketplace

A student project to learn about web development, databases, and e-commerce basics.

## Features

- User registration and login
- Create, edit, and delete listings with images
- Browse listings by category
- Add items to cart and checkout
- View your purchases and sales
- Simple dashboard for managing your listings

## Getting Started

### Prerequisites

- PHP 8+
- MySQL/MariaDB
- [Bun](https://bun.sh/) for  TailwindCSS libraries
- `make` to use build scripts

### Setup

1. **Clone the repo:**

   ```sh
   git clone https://github.com/prince-b4u/mmp
   cd mmp
   ```

2. **Install dependencies:**

   ```sh
   bun install
   ```

3. **Set up the database:**
   - Create a database called `eduvos`.
   - Import the SQL file in [`./db/`](./init.sql)

4. **Configure database connection:**
   - Edit [`src/config.php`](src/config.php) with your DB username and password.

5. **Build CSS:**

   ```sh
   make build
   ```

6. **Run the app:**

   ```sh
   make start
   ```

   Then open [website](http://localhost:9090) in your browser.

## Folder Structure

- `src/` — PHP source files
- `src/components/` — Reusable components
- `src/js/` — JavaScript files
- `src/css/` — CSS files (built with Tailwind CSS)
- `uploads/` — Uploaded images

## License

This project is licensed under the [MIT License](LICENSE).
