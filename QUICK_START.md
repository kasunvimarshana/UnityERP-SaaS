# Unity ERP SaaS - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Prerequisites Check
```bash
# Check PHP version (need 8.3+)
php --version

# Check Composer
composer --version

# Check Node.js (need 20+)
node --version

# Check NPM
npm --version

# Check MySQL/PostgreSQL
mysql --version  # or
psql --version
```

### Step 1: Clone and Setup (1 min)
```bash
# Clone the repository
git clone https://github.com/kasunvimarshana/UnityERP-SaaS.git
cd UnityERP-SaaS

# Install backend dependencies
cd backend
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 2: Database Setup (2 min)
```bash
# Edit .env file with your database credentials
# Default settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=unity_erp
DB_USERNAME=root
DB_PASSWORD=

# Create the database
mysql -u root -p -e "CREATE DATABASE unity_erp;"

# Run migrations
php artisan migrate

# (Optional) Seed master data
php artisan db:seed
```

### Step 3: Start Backend (1 min)
```bash
# From backend directory
php artisan serve

# Backend API now running at:
# http://localhost:8000
```

### Step 4: Frontend Setup (1 min)
```bash
# Open new terminal
cd ../frontend

# Install dependencies
npm install

# Start development server
npm run dev

# Frontend now running at:
# http://localhost:5173
```

## ✅ Verify Installation

### Test API
```bash
curl http://localhost:8000/api/health
```

### Test Frontend
Open browser: `http://localhost:5173`

## 📁 Project Structure

```
UnityERP-SaaS/
├── backend/              # Laravel 11 API
│   ├── app/
│   │   ├── Core/        # Base architecture
│   │   ├── Modules/     # Business domains
│   │   └── Models/      # Eloquent models
│   ├── database/
│   │   ├── migrations/  # Database schema
│   │   └── seeders/     # Sample data
│   └── routes/          # API routes
│
├── frontend/            # Vue.js 3 app
│   ├── src/
│   │   ├── components/ # Reusable components
│   │   ├── views/      # Pages
│   │   └── services/   # API clients
│   └── public/         # Static assets
│
└── docs/               # Documentation
```

## 🔑 Key Concepts

### Multi-Tenancy
Every request is automatically scoped to the authenticated user's tenant:

```php
// Automatically filtered by tenant_id
$products = Product::all();

// No need to add ->where('tenant_id', ...)
```

### Repositories
Data access is abstracted:

```php
// In Service
$product = $this->productRepository->findById($id);
```

### Services
Business logic lives in services:

```php
// In Controller
$product = $this->productService->create($data);
```

### Audit Trails
Created/updated by is automatic:

```php
// Automatically set
$product->created_by = auth()->id();
$product->updated_by = auth()->id();
```

## 🎯 Quick Tasks

### Create a Product
```bash
# Using Tinker
php artisan tinker

Product::create([
    'sku' => 'PROD-001',
    'name' => 'Test Product',
    'type' => 'inventory',
    'buying_price' => 100,
    'selling_price' => 150,
    'is_active' => true,
]);
```

### Run Tests
```bash
php artisan test
```

### Generate API Docs
```bash
php artisan l5-swagger:generate
# Visit: http://localhost:8000/api/documentation
```

### Check Routes
```bash
php artisan route:list
```

## 🐛 Troubleshooting

### Common Issues

#### "Class not found"
```bash
composer dump-autoload
```

#### "Permission denied" on storage
```bash
chmod -R 775 storage bootstrap/cache
```

#### "Database connection failed"
```bash
# Check .env file
# Verify database exists
# Check credentials
php artisan config:clear
```

#### Port already in use
```bash
# Backend on different port
php artisan serve --port=8001

# Frontend on different port
npm run dev -- --port 5174
```

## 📚 Next Steps

### For Developers
1. Read [ARCHITECTURE.md](./ARCHITECTURE.md) - Understand the design
2. Read [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md) - See what's done
3. Read [PROJECT_README.md](./PROJECT_README.md) - Detailed documentation

### For Contributors
1. Check open issues on GitHub
2. Follow the coding standards
3. Write tests for new features
4. Update documentation

### For Users
1. Create your first tenant
2. Setup organizations and branches
3. Add products to catalog
4. Start managing inventory

## 🔧 Development Tools

### Recommended VS Code Extensions
- PHP Intelephense
- Laravel Blade Snippets
- Vue Language Features (Volar)
- ESLint
- Prettier

### Useful Commands

#### Backend
```bash
# Create migration
php artisan make:migration create_table_name

# Create model
php artisan make:model Module/ModelName

# Create controller
php artisan make:controller Module/ControllerName

# Run specific test
php artisan test --filter TestName

# Clear all caches
php artisan optimize:clear
```

#### Frontend
```bash
# Build for production
npm run build

# Type check
npm run type-check

# Lint and fix
npm run lint
```

## 🎓 Learning Resources

### Laravel
- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

### Vue.js
- [Vue 3 Documentation](https://vuejs.org/)
- [Vue Router](https://router.vuejs.org/)
- [Pinia](https://pinia.vuejs.org/)

### Architecture
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Repository Pattern](https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html)

## 💬 Get Help

### Documentation
- Full docs in `/docs` directory
- Inline code comments
- Architecture diagrams (planned)

### Support
- GitHub Issues for bugs
- GitHub Discussions for questions
- Stack Overflow with tag `unity-erp`

## 🎉 You're Ready!

The platform is now running. Start building your ERP solution!

### Quick Checklist
- [ ] Backend running on :8000
- [ ] Frontend running on :5173
- [ ] Database connected
- [ ] Migrations completed
- [ ] Can access API docs

### What's Next?
1. Create authentication endpoints
2. Build product management API
3. Implement inventory tracking
4. Develop frontend UI
5. Add more modules

---

**Need help?** Check the documentation or open an issue on GitHub.

**Ready to contribute?** Read CONTRIBUTING.md (coming soon).

Happy coding! 🚀
