# Project: PABW Marketplace AI

## Commands
- **Run Migrations & Seeds**: `sudo docker run --rm -v $(pwd):/app -w /app composer php artisan migrate --seed --force`
- **Install Dependencies**: `sudo docker run --rm -v $(pwd):/app -w /app composer install`
- **Artisan**: `sudo docker run --rm -v $(pwd):/app -w /app composer php artisan <command>`

## API Routes
### Auth
- `POST /api/register`: Register new user
- `POST /api/login`: Login user
- `POST /api/logout`: Logout (Auth required)

### Products
- `GET /api/products`: List all products
- `GET /api/products/context`: Get all products as JSON for AI
- `GET /api/products/{id}`: Get product details

### AI Chat
- `POST /api/chat`: Chat with Ollama (Guest allowed)
- `GET /api/chat/history`: Get user's chat history (Auth required)

### Cart (Auth required)
- `GET /api/cart`: List cart items
- `POST /api/cart`: Add/update item in cart
- `DELETE /api/cart/{id}`: Remove item from cart

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
