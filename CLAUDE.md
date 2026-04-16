# Project: PABW Marketplace AI

## Commands
- **Run Migrations & Seeds**: `sudo docker run --rm -v $(pwd):/app -w /app composer php artisan migrate --seed --force`
- **Install Dependencies**: `sudo docker run --rm -v $(pwd):/app -w /app composer install`
- **Artisan**: `sudo docker run --rm -v $(pwd):/app -w /app composer php artisan <command>`

## API Routes
- `GET /api/products`: List all products (supports filters: `category_id`, `max_price`, `search`)
- `GET /api/products/{id}`: Get product details

## Database Schema
### Categories
- `id`: Primary Key
- `name`: Category name (e.g., Keyboard, Mouse, Headset)

### Products
- `id`: Primary Key
- `name`: Product name
- `description`: Detailed description
- `price`: Product price
- `stock`: Available stock
- `category_id`: Foreign key to categories
- `image_url`: Product image URL
