# MyPastebin

A modern, responsive, and secure Pastebin application built with **PHP** (Vanilla), **MySQL**, **Tailwind CSS**, and **Bootstrap 5**. It features a stunning Glassmorphism UI, a robust reporting system, and dedicated administrative tools.

## Features

### 🎨 User Interface
- **Glassmorphism Design**: Modern, translucent aesthetics with custom blur effects.
- **Responsive Layout**: specific mobile and desktop views using Tailwind CSS.
- **Dark Mode**: High-contrast, easy-on-the-eyes dark theme.
- **Syntax Highlighting**: Automatic code highlighting for various languages.

### 📝 Paste Management
- **Create Pastes**: Guest and User paste creation with syntax selection.
- **Edit/Delete**: Full CRUD operations for owners and admins.
- **Sharing**:
    - **Public Links**: Copy-pasteable links.
    - **User Collaboration**: Grant 'edit' permissions to other registered users.
- **Featured Pastes**: Admins can feature notable pastes to appear on the homepage.

### 🛡️ Administration & Moderation
- **Super Admin Dashboard**:
    - dedicated `dimono` super admin account.
    - **Reports Tab**: View, dismiss, or delete reported pastes.
    - **User Management**: View users, promote/demote admins, or ban users.
    - **Global Statistics**: Real-time counts of users, pastes, views, and reports.
- **On-Card Admin Tools**:
    - **Summary Modal**: 'i' icon on cards (admin-only) to see quick stats (Reports, Shares, Views).

### 🚨 Reporting System
- Users can flag inappropriate pastes.
- Admins receive alerts in the dashboard.

## Installation

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/pgwiz/pastebin.git
    cd pastebin
    ```

2.  **Database Setup**
    - Create a MySQL database (e.g., `pastebin`).
    - Import the schema:
      ```bash
      mysql -u root -p pastebin < schema.sql
      ```

3.  **Configuration**
    - Update `db.php` with your database credentials:
      ```php
      $host = '127.0.0.1';
      $db   = 'pastebin';
      $user = 'root';
      $pass = '';
      ```

4.  **Admin Account**
    - A super admin account `dimono` is created if you ran the setup script or manually insert it.
    - To create one manually, insert a user with `is_superadmin = 1` in the `users` table.

## Tech Stack
- **Backend**: PHP 8+ (PDO for Database)
- **Frontend**: HTML5, Vanilla JS, Tailwind CSS (via local CDN), Bootstrap 5.
- **Database**: MySQL / MariaDB.
- **Assets**: FontAwesome 6, Google Fonts (Inter).

## License
MIT License.
