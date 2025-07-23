# CommentsApp: Universal Commenting System Backend

CommentsApp adalah aplikasi komentar management universal seperti discuss forum, dibangun dengan praktik pengembangan modern.

Aplikasi ini adalah sarana untuk saya membangun aplikasi dengan clean code, scalable, dan monitorable.

---

## Core Features

- **User Authentication**: Using sanctum for user authentication.
- **Role-Based Permission Control**: Using Spatie Laravel Permission to control user permissions.
- **Token-Based API**: Stateless API with support for token refresh.
- **Profile Management**: CRUD user.
- **Commenting System**: CRUD comment. likes, upvote, downvote, and report.
- **Asynchronous Job Processing**: Using laravel horizon for offloading heavy tasks like logging and statistics to background queues.
- **Centralized Exception Handling**: A custom, reusable exception system that standardizes error responses and logging.
- **Detailed Activity Logging**: Using Spatie Activity Log for tracking and custom UI for track logs.

## Tech Stack

- **Backend**: PHP 8+, Laravel 12+
- **Database**: MySQL, Redis
- **API Authentication**: Laravel Sanctum
- **Role Management**: Spatie Laravel Permission
- **Queue & Job Management**: Laravel Horizon
- **Development Monitoring**: Laravel Telescope
- **API Response Serialization**: League/Fractal
- **Activity Logging**: Spatie Activity Log
- **Exception Monitoring**: Sentry
- **Local Environment**: Docker, Laravel Sail

## Getting Started

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop/)
- [WSL 2](https://docs.microsoft.com/en-us/windows/wsl/install) (for Windows users)

### Local Development Setup

1.  **Clone repository:**
    ```bash
    git clone https://github.com/fajar-anggara/CommentsApp.git
    cd CommentsApp
    ```

2.  **Create environment file:**
    ```bash
    cp .env.example .env
    ```

3.  **Start application containers using Sail:**
    ```bash
    ./vendor/bin/sail up -d
    ```

4.  **Install dependencies:**
    ```bash
    ./vendor/bin/sail composer install
    ```

5.  **Generate application key:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Run database migrations and seeders:**
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

### Running the Application

-   The application available at `http://localhost`.
-   Run horizon for job processing:
-   ```bash
    ./vendor/bin/sail artisan horizon
    ```

## Testing

Run the full test suite using the following command:

```bash
./vendor/bin/sail artisan test
```

## Important Endpoints

-   **API Documentation**: `http://localhost/api/documentation`
-   **Horizon Dashboard**: `http://localhost/horizon`
-   **Activity Logs UI**: `http://localhost/activity-logs`
-   **Telescope Dashboard** (Local Only): `http://localhost/telescope`

## Contact

-   **Email**: muhamadfajaranggara@gmail.com
-   **LinkedIn**: https://www.linkedin.com/in/moh-fajar-anggara-252219180/
