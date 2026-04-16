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
- `GET /api/user`: Get profile (Auth required)
- `PUT /api/user`: Update profile (Auth required)

### Products
- `GET /api/products`: List all products (supports pagination, search, filters, and sorting)
  - `page`: Page number (default: 1)
  - `per_page`: Items per page (default: 12)
  - `search`: Robust search in name, description, and category
  - `category_id`: Filter by category
  - `max_price`: Filter by maximum price
  - `sort`: `price_asc`, `price_desc`, `latest` (default)
- `GET /api/products/context`: Get all products as JSON for AI
- `GET /api/products/{id}`: Get product details

### AI Chat
- `POST /api/chat`: Chat with Ollama (Guest allowed, contextual for members)
- `GET /api/chat/history`: Get user's chat history (Auth required)

### Cart (Auth required)
- `GET /api/cart`: List cart items
- `POST /api/cart`: Add/update item in cart
- `DELETE /api/cart/{id}`: Remove item from cart

### Coupons
- `POST /api/coupons/validate`: Validate a coupon code (Auth required)
  - `code`: The coupon code
  - `total_price`: The current total price before discount

### Orders (Auth required)
- `GET /api/orders`: List user orders
- `POST /api/orders`: Create order (Checkout)
  - `coupon_code`: (Optional) Apply a coupon code during checkout
- `GET /api/orders/{id}`: Get order details
- `POST /api/orders/{id}/pay`: Simulate payment

### Admin (Auth & Admin required)
- `GET /api/admin/products`: List all products (Admin)
- `POST /api/admin/products`: Create product
- `PUT /api/admin/products/{id}`: Update product
- `DELETE /api/admin/products/{id}`: Delete product
- `GET /api/admin/orders`: List all orders (Global)
- `PUT /api/admin/orders/{id}/status`: Update order status
- `GET /api/admin/coupons`: List all coupons
- `POST /api/admin/coupons`: Create coupon
- `PUT /api/admin/coupons/{id}`: Update coupon
- `DELETE /api/admin/coupons/{id}`: Delete coupon

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
