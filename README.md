# Product Management System

## Features
- Imports products and their variations.
- Synchronizes data with an external API.
- Soft-deletes outdated products.
- Schedules daily updates.

## Setup Instructions
1. Clone the repository.
2. Run `composer install`.
3. Configure `.env` file.
4. Run `php artisan migrate`.
5. Use `php artisan import:products` to import products from CSV.
6. Open the crontab editor in your server’s terminal: `crontab -e` , Add the following line to schedule the synchronization command to run daily at midnight `0 0 * * * php /path-to-your-project/artisan products:synchronize >> /path-to-your-project/storage/logs/sync.log 2>&1` ,Press CTRL + O to save and CTRL + X to exit if using nano.Press Esc and type :wq if using vim.then check list of jobs `crontab -l`
 

## Testing
Run tests with `php artisan test`.
