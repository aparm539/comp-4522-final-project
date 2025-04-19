# Chemical Inventory Management System

A web app for tracking and managing chemical containers. Built with Laravel and Filament Admin Panel. Made for the MRU chemistry department.

## Table of Contents

-   [Features](#features)
-   [Technologies Used](#technologies-used)
-   [Folder Structure](#folder-structure)
-   [Developer Setup](#developer-setup)
-   [Application Components](#application-components)
-   [Reconciliation Feature](#reconciliation-feature)

## Features

-   Container tracking with unique barcodes
-   Chemical management
-   Location and storage cabinet organization
-   User roles and permissions (Admin, Researcher, Viewer)
-   Reconciliation system
-   Export and import functionality

## Technologies Used

### Backend

-   **PHP 8.2+**
-   **Laravel 12** - PHP web framework
-   **Filament 3** - Admin panel and CRUD interface
-   **Laravel Excel** - Import/export functionality
-   **Laravel DomPDF** - PDF generation

### Frontend

-   **TailwindCSS 4**
-   **Blade**
-   **Livewire**

### Development/Testing

-   **Vite** - Frontend build tool
-   **PHPUnit** - Testing framework
-   **Laravel Pint** - PHP code style fixer
-   **Laravel Pail** - Log viewer

## Folder Structure

The application follows Laravel's standard directory structure:

-   **app/** - Core application code
    -   **Filament/** - Admin panel components and resources
    -   **Livewire/** - Livewire components
    -   **Models/** - Eloquent model definitions
    -   **Policies/** - Authorization policies
    -   **Providers/** - Service providers
-   **bootstrap/** - Application bootstrapping files
-   **config/** - Configuration files
-   **database/** - Database migrations, seeders, and factories
-   **public/** - Publicly accessible files
-   **resources/** - Frontend resources
    -   **css/** - CSS styles
    -   **js/** - JavaScript files
    -   **views/** - Blade templates
-   **routes/** - Route definitions
-   **storage/** - Application storage
-   **tests/** - Automated tests
-   **vendor/** - Composer dependencies

## Developer Setup

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js and npm
-   MySQL
-   Git
-   Docker

### Installation Steps

1. Clone the repository:

    ```bash
    git clone [repository-url]
    cd comp-4522-final-project
    ```

2. Install PHP dependencies:

    ```bash
    composer install
    ```

3. Install JavaScript dependencies:

    ```bash
    npm install
    ```

4. Create environment file and generate app key:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. Start Laravel Sail:

    ```bash
    ./vendor/bin/sail up -d
    ```

6. Run database migrations and seed initial data:

    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

7. Start the development server:

    ```bash
    ./vendor/bin/sail npm run dev
    ```

8. Visit http://localhost in your browser and login using one of the users created in the UserSeeder.php file

## Application Components

### Models and Relationships

-   **User** - System users with role-based permissions
-   **Role** - User roles (Admin, Researcher, Viewer)
-   **Container** - Chemical containers with unique barcodes
-   **Chemical** - Chemical substances
-   **Location** - Physical locations for containers
-   **StorageCabinet** - Storage units for chemicals
-   **UnitOfMeasure** - Measurement units for chemicals
-   **Reconciliation** - Inventory reconciliation records
-   **ReconciliationItem** - Individual items in reconciliation

### Authentication and Authorization

The system implements a role-based access control system:

-   **Admin** - Complete access to all features
-   **Researcher** - Can manage containers and perform reconciliations
-   **Viewer** - Read-only access to inventory data

## Reconciliation Feature

The reconciliation feature provides a systematic way to verify and account for all chemical containers in the inventory. It helps ensure the accuracy of inventory records and identifies any discrepancies between the system records and the actual physical inventory.

### Overview

Reconciliation is the process of comparing the expected inventory (what the system shows) with the actual physical inventory (what exists in the laboratory). This helps in:

-   Identifying missing or misplaced containers
-   Verifying the quantities of chemicals
-   Ensuring compliance with safety and regulatory requirements
-   Maintaining accurate inventory records

### How It Works

1. **Initiation**: Reconciliations can be started for:

    - Individual locations
    - All locations at once (using the "Create Reconciliations for All Locations" action)

2. **Process**:

    - Each reconciliation is associated with a specific location
    - The system automatically creates reconciliation items for each container in the storage cabinets at that location
    - Each reconciliation item tracks:
        - Expected quantity (from system records)
        - Actual quantity (to be filled during the physical count)
        - Reconciliation status (whether the item has been checked)

3. **Reconciliation States**:

    - **Ongoing**: The reconciliation process is in progress
    - **Completed**: All items have been verified and the reconciliation is finished
    - **Stopped**: The reconciliation has been halted before completion

4. **Item Verification**:
    - Staff scan or manually enter container barcodes
    - Actual quantities are recorded
    - Items are marked as "reconciled" once verified

### Workflow Example

1. An administrator initiates a reconciliation for a specific laboratory location
2. The system generates a list of all containers expected to be at that location
3. Staff physically count and verify each container
4. For each container found:
    - The barcode is scanned
    - The actual quantity is recorded
    - The container is marked as "reconciled"
5. After all containers are checked, discrepancies are reviewed:
    - Missing containers (expected but not found)
    - Unexpected containers (found but not in the system record for that location)
    - Quantity differences
6. Actions are taken to resolve discrepancies
7. The reconciliation is marked as "completed"

### Data Structure

The reconciliation feature is built on two main models:

1. **Reconciliation**:

    - Associated with a location
    - Tracks status (ongoing, completed, stopped)
    - Records start and end times
    - Stores notes about the reconciliation process

2. **ReconciliationItem**:
    - Links a specific container to a reconciliation
    - Records expected and actual quantities
    - Tracks whether the item has been reconciled

### Reporting

The reconciliation feature provides reporting capabilities that allow administrators to:

-   View the status of ongoing reconciliations
-   Review completed reconciliations
-   Identify patterns of discrepancies
-   Generate compliance reports for regulatory purposes
