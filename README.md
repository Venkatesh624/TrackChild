# TrackChild

A web application for reporting and tracking missing children by connecting guardians with response agencies.

## ✨ Key Features

* **Dual User Roles**: Separate portals for **Users** (who report cases) and **Agencies** (who manage cases).
* **Case Reporting**: Users can submit detailed reports for missing children.
* **Admin Dashboard**: Agencies can view and manage all submitted reports, update case statuses, and log found locations.
* **Notifications**: Automatically notifies users about status updates on their reported cases.
* **Feedback System**: Allows users to provide ratings and feedback on the service.

## 🛠️ Technology Stack

* **Backend**: PHP
* **Database**: MySQL / MariaDB
* **Frontend**: HTML, CSS, Bootstrap 5

## 🚀 Getting Started

1.  **Prerequisites**: Ensure you have a local server environment like **XAMPP** or **WAMP** installed.

2.  **Clone**: Clone the repository into your server's web directory (usually named `htdocs`).

3.  **Database Setup**:
    * Open phpMyAdmin and create a new, empty database (e.g., `trackchild_db`).
    * You will need to manually create the necessary tables (`users`, `agencies`, `children`, `notifications`, etc.).

4.  **Configure Connection**:
    * Create or edit the `includes/db.php` file.
    * In this file, add your database credentials (host, database name, username, password).

5.  **Run the App**:
    * Open your browser and go to `http://localhost/YourProjectFolder/pages/`.
