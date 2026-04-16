# API Documentation - Marketplace Gaming Gear

**Base URL:** `http://localhost:5000/api`
**Protocol:** REST API (JSON)

---

## 1. Produk (Gaming Gear)

### [GET] /products
Mendapatkan semua daftar produk untuk ditampilkan di katalog.

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid-1",
      "name": "Logitech G Pro",
      "category": "Mouse",
      "price": 1500000,
      "image_url": "https://link-foto.com/mouse.jpg"
    }
  ]
}
```

## 2. AI Chatbot

### [POST] /ai/chat
Mengirim pertanyaan user ke chatbot AI.

**Request Body:**
```json
{
  "message": "Rekomendasi mouse wireless di bawah 1 juta"
}

{
  "success": true,
  "data": {
    "reply": "Tentu! Saya merekomendasikan Razer Orochi V2 karena...",
    "suggested_products": [
      {
        "id": "uuid-1",
        "name": "Razer Orochi V2"
      }
    ]
  }
}
```
