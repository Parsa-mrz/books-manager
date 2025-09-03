# 📚 Book Manager

A WordPress plugin for managing book information.  
It registers a custom post type (**Books**), stores metadata (like ISBNs) in a custom database table, and provides an admin page with a table view for managing book records.

Built with ❤️ using the [Rabbit Framework](https://github.com/veronalabs/rabbit) and modern WordPress practices (service providers, dependency injection, PSR-4 autoloading).

---

## 🚀 Features

- Custom Post Type: `book`
- Custom Database Table for storing ISBN and metadata
- Admin metabox for editing book info
- Admin List Table (`WP_List_Table`) for managing all books in one place
- Service Provider pattern with [Rabbit Framework](https://github.com/veronalabs/rabbit)
- Composer + Autoloading
- Translation-ready (`.pot` file included)

---

## 📦 Installation

### 1. Clone the repo
```bash
git clone https://github.com/Parsa-mrz/book-manager.git
cd book-manager
```

### 2. Install dependencies
```bash
composer install
```
### 3. Activate the plugin
```bash
wp-env start
```

## 🧩 Tech Stack
- WordPress
- Rabbit Framework
- Composer
- PHP 8.2+
- PSR-4 Autoloading
