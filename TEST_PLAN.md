# Test Plan: Gaming Gear Marketplace with AI Integration

## 1. Introduction
This document outlines the testing strategy and test cases for the Gaming Gear Marketplace, focusing on AI integration, user flows, and UI/UX requirements.

## 2. Test Objectives
- Ensure a seamless transition from guest to member.
- Verify AI chatbot accuracy in product recommendations including technical specifications.
- Validate UI design compliance (Black, White, Red palette, Sidebar).
- Confirm functionality of key features like "Add to Cart" and Chat History.

## 3. Scope
- Guest-to-Member Flow
- AI Recommendations (Ollama Qwen 2.5) with advanced attributes (DPI, Switch Type, etc.)
- UI/UX Design Verification (Dark Theme, Sidebar Menu)
- Core Marketplace Functionalities
- Chat History Persistence

## 4. Test Scenarios

### 4.1. Guest-to-Member Flow
| ID | Test Case | Description | Expected Result |
|----|-----------|-------------|-----------------|
| GTM-01 | Guest Chat | Guest uses the AI chat to ask about products. | Chat responds normally. |
| GTM-02 | Add to Cart (Guest) | Guest clicks "Add to Cart" button in the AI chat widget. | User is redirected to the Login/Register page. |
| GTM-03 | Post-Login Persistence | Guest adds item, logs in, and verifies if the item can now be added. | Successfully adds to cart after login. |

### 4.2. AI Accuracy Testing
| ID | Test Case | Description | Expected Result |
|----|-----------|-------------|-----------------|
| AI-01 | Budget Recommendation | Ask: "Recommend a mouse under $60". | AI suggests only products with `price <= 60`. |
| AI-02 | Category Filtering | Ask: "Show me mechanical keyboards". | AI suggests only keyboards. |
| AI-03 | Stock Awareness | Ask for a specific product that is out of stock. | AI mentions it is out of stock or suggests an alternative. |
| AI-04 | JSON Interpretation | Verify AI correctly reads product attributes from the database JSON. | Attributes match database records. |
| AI-05 | Advanced Attributes | Ask for products with specific `switch_type`, `dpi`, `connectivity`, `sensor`, or `weight`. | AI accurately filters and mentions these specs in the response. |

### 4.3. UI/UX Verification
| ID | Test Case | Description | Expected Result |
|----|-----------|-------------|-----------------|
| UI-01 | Color Palette | Inspect CSS for primary colors. | Uses #000000 (Black background), #FFFFFF (White text), and #FF0000 (Red accents). |
| UI-02 | Sidebar Navigation | Verify sidebar existence and menu items. | Sidebar contains: Chat AI, Keranjang, Pengaturan, Profil, Cek Pesanan. |
| UI-03 | Responsiveness | Test on mobile, tablet, and desktop views. | Layout adjusts without breaking; Chat remains accessible. |
| UI-04 | Product Widget Rendering | In chat, ask for a product recommendation. | AI response includes "Product Widget" cards with image, name, price, and "Add to Cart" button. |

### 4.4. Functionality
| ID | Test Case | Description | Expected Result |
|----|-----------|-------------|-----------------|
| FN-01 | Add to Cart Button | Click "Add to Cart" within the chat widget as a logged-in user. | Item added to cart; notification shown; cart count updates. |
| FN-02 | Product Search | Search for products via the search bar. | Relevant products are displayed. |

### 4.5. Chat History
| ID | Test Case | Description | Expected Result |
|----|-----------|-------------|-----------------|
| CH-01 | History Saving | User chats with AI, navigates away, and comes back. | Chat history is preserved. |
| CH-02 | Session Persistence | User logs out and logs back in. | Chat history is still visible for the account. |

## 5. Technical Verification
- **API Response:** `GET /api/products` should return valid JSON with correct types including advanced attributes.
- **AI Latency:** Response time for Ollama should be within acceptable limits (e.g., < 5s).
- **Database Sync:** Verify that `team-db` reflects the same data as the local SQLite database.

## 6. Edge Cases
- Asking the AI non-gaming gear related questions (Off-topic handling).
- Rapidly clicking "Add to Cart" multiple times.
- Budget recommendation with no matching products (AI should handle gracefully).
