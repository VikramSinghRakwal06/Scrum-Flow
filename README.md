# 🚀 ScrumFlow

ScrumFlow is a professional, multi-tenant **Agile Project Management Tool** built with Vanilla PHP. It features a modern "Glassmorphism" UI and a robust Role-Based Access Control (RBAC) system.



## ✨ Key Features

* **Multi-Tenancy:** Securely separated workspaces for different organizations using unique Org IDs.
* **Role-Based Access Control (RBAC):**
    * **Owner:** Full control over the organization and team roles.
    * **Manager:** Can assign tasks and perform Quality Assurance (QA) approvals.
    * **Member:** Can create and edit their own tasks, and submit them for review.
* **Agile Workflow:** Tasks move through a structured pipeline: `Backlog` → `In-Progress` → `Testing` (QA) → `Completed`.
* **Sprint Management:** Group tasks into time-boxed Sprints for better project planning.
* **Modern UI:** Responsive dark-mode interface built with Bootstrap 5.

## 🛠️ Technical Stack

* **Backend:** PHP 8.x
* **Database:** MySQL (PDO for secure queries)
* **Environment:** Managed via `vlucas/phpdotenv`
* **Dependency Management:** Composer
* **Server:** Apache (with custom `.htaccess` routing)

## 🚀 Quick Start

### 1. Prerequisites
* XAMPP / WAMP / MAMP (PHP 8.0+)
* [Composer](https://getcomposer.org/)

### 2. Installation
```bash
# Clone the repo
git clone [https://github.com/VikramSinghRakwal06/Scrum-Flow.git](https://github.com/VikramSinghRakwal06/Scrum-Flow.git)

# Install dependencies
composer install