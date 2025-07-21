# Universal Comments App 

Sebuah backend app untuk aplikasi management komentar universal

## About

Ini merupakan apikasi REST API untuk memanage komentar seperti discuss. yang nantinya akan di embbed oleh front-end secara terpisah.
- User Management (baru User Register)

## Features

### Sudah terbangun

- User Register, Login, Logout, get my data
- Refresh API Token

### Sedang dikerjakan

- User Login

## Current Status

Yang sudah terimplementasi dan di test mengguakan insomnia:
- User update frofile


## API Documentation

None

## Testing

None

## Tech Stack

- **PHP 8+** - Language
- **Laravel Sanctum** - API and user management
- **Spatie Laravel Permission** - Management Role
- **Laravel Horizon** - Job Watcher
- **Laravel Telescope** - Monitoring when development
- **Spatie Query-builder** - Simplify Query
- **League Fractal** - Reaponse and Request converter and serializer
- **Spatie Activity Log** - Logging
- **Sentry** - Exception watcher


## Getting Started

### Prerequisites
- PHP 8+
- Laravel 12+

### Setup local

1. Clone the repository
   ```bash
   git clone https://github.com/fajar-anggara/CommentsApp
   cd CommentsApp
   ```

2. Run the application
   If you using windows, use docker wsl ubuntu un order to run Laravel Sail and Horizon.
   ```bash
   ./Vendor/bin/sail up -d
   ```

## Contact

- **Email**: muhamadfajaranggara@gmail.com
- **LinkedIn**: https://www.linkedin.com/in/moh-fajar-anggara-252219180/
