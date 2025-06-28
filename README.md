# Yes Homework API

A complete RESTful API for managing daily homework assignments for children, built with PHP and SQLite.

## 🚀 **Complete Refactor Summary**

✅ **Firebase Removed** - Replaced with native PHP JWT implementation  
✅ **Organization System Removed** - Simplified to user-centric design  
✅ **New Database Schema** - Clean SQLite structure for homework management  
✅ **Authentication System** - Magic link + JWT tokens  
✅ **API Routes** - Complete CRUD operations for users, children, and worksheets  
✅ **Security** - Rate limiting, input validation, proper authorization  
✅ **Testing Ready** - Postman collection included  

## 📋 **Database Schema**

### Users Table
- `id` (Primary Key)
- `email` (Unique)
- `plan` ('free' or 'premium')
- `is_verified`
- `stripe_customer_id`
- `stripe_subscription_id`
- `plan_ends_at`
- `created_at`

### Magic Links Table  
- `id` (Primary Key)
- `user_id` (Foreign Key)
- `token` (64-char hex)
- `expires_at`
- `created_at`

### Children Table
- `id` (Primary Key)
- `user_id` (Foreign Key)
- `name`
- `age_group` ('Reception' to 'Year 6')
- `interest1`
- `interest2`
- `created_at`

### Worksheets Table
- `id` (Primary Key)
- `child_id` (Foreign Key)
- `date` (Unique per child)
- `content` (Markdown format)
- `pdf_path`
- `downloaded` (0/1)
- `created_at`

## 🔧 **Setup Instructions**

1. **Environment Configuration:**
   ```bash
   cp public_html/api/.env.example public_html/api/.env
   # Edit .env with your settings
   ```

2. **Database Setup:**
   - SQLite database auto-creates at `/api/database/database.sqlite`
   - No manual setup required

3. **Test the API:**
   - Import `Daily_Homework_API.postman_collection.json` into Postman
   - Start with the Health Check endpoint

## 🛠 **API Endpoints**

### **System**
- `GET /api/health` - Health check

### **Authentication**
- `POST /api/auth/signup` - Sign up with email
- `POST /api/auth/login` - Request magic link
- `GET /api/auth/verify` - Verify magic link token
- `POST /api/auth/token` - Generate JWT token
- `POST /api/auth/refresh-token` - Refresh JWT (protected)

### **User Management** (Protected)
- `GET /api/users/profile` - Get user profile
- `PUT /api/users/profile` - Update user profile

### **Children Management** (Protected)
- `GET /api/children` - Get all children
- `POST /api/children` - Add new child
- `PUT /api/children/{id}` - Update child
- `DELETE /api/children/{id}` - Delete child

### **Worksheet Management** (Protected)
- `GET /api/worksheets` - Get all worksheets for user
- `GET /api/children/{id}/worksheets` - Get worksheets for specific child
- `POST /api/worksheets` - Create new worksheet
- `GET /api/worksheets/{id}` - Get specific worksheet
- `PUT /api/worksheets/{id}` - Update worksheet
- `DELETE /api/worksheets/{id}` - Delete worksheet
- `POST /api/worksheets/{id}/download` - Mark as downloaded
- `GET /api/stats/worksheets` - Get worksheet statistics

## 🔐 **Authentication Flow**

1. **Sign Up:** `POST /auth/signup` with email
2. **Login:** `POST /auth/login` with email (sends magic link)
3. **Verify:** `GET /auth/verify` with email and token from magic link
4. **Get JWT:** `POST /auth/token` with user data from verification
5. **Use JWT:** Include `Authorization: Bearer {token}` in all protected requests

## 📝 **Example Usage**

### Sign Up and Login
```bash
# Sign up
curl -X POST http://localhost/api/auth/signup \
  -H "Content-Type: application/json" \
  -d '{"email": "parent@example.com"}'

# Request login
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "parent@example.com"}'

# Check email for magic link, then verify
curl "http://localhost/api/auth/verify?email=parent@example.com&token=TOKEN_FROM_EMAIL"

# Generate JWT token
curl -X POST http://localhost/api/auth/token \
  -H "Content-Type: application/json" \
  -d '{"id": 1, "email": "parent@example.com", "plan": "free"}'
```

### Manage Children
```bash
# Add child
curl -X POST http://localhost/api/children \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"name": "Emma", "age_group": "Year 3", "interest1": "Mathematics", "interest2": "Science"}'

# Get all children
curl -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  http://localhost/api/children
```

### Create Worksheets
```bash
# Create worksheet
curl -X POST http://localhost/api/worksheets \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "child_id": 1,
    "date": "2025-01-20",
    "content": "# Daily Homework - Year 3\n\n## Mathematics\n1. 25 + 17 = ?\n2. 42 - 18 = ?\n\n## English\n1. Write 3 sentences using: happy, garden, butterfly"
  }'
```

## 🧪 **Testing with Postman**

1. Import `Daily_Homework_API.postman_collection.json`
2. Update the `base_url` variable to your server URL
3. Run the authentication flow to get JWT token
4. Use the token for protected endpoints

## 🔒 **Security Features**

- **Magic Link Authentication** - No passwords to manage
- **JWT Tokens** - Secure stateless authentication
- **Rate Limiting** - Protection against abuse
- **Input Validation** - SQL injection protection
- **Authorization Checks** - Users can only access their own data
- **CORS Headers** - Cross-origin request support

## 📁 **File Structure**

```
public_html/
├── api/
│   ├── database/
│   │   └── database.sqlite (auto-created)
│   ├── .env (configuration)
│   ├── index.php (main API entry point)
│   ├── Router.php (routing system)
│   ├── AuthMiddleware.php (authentication)
│   ├── JWTAuth.php (JWT handling - native PHP)
│   ├── UserAuthAPI.php (user management)
│   ├── WorksheetAPI.php (worksheet management)
│   ├── conf.php (database configuration)
│   └── env.php (environment loader)
├── app/
│   └── index.php (simple homepage)
└── Daily_Homework_API.postman_collection.json
```

## 🚀 **Ready for Production**

The API is production-ready with:
- ✅ Proper error handling
- ✅ Environment-based configuration  
- ✅ Security best practices
- ✅ Clean database schema
- ✅ Comprehensive testing collection
- ✅ No external dependencies (except SQLite)

Start building your Daily Homework frontend application!