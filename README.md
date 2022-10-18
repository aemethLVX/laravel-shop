# Installation

### Create storage symlink
- Run the command `php artisan storage:link`

### Set File System Disk
- Set `FILESYSTEM_DISK=public` in .env file

### Install Laravel Telescope
- Run the command `php artisan telescope:install`
- Run the command `php artisan migrate`
