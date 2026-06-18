# Fashion BD — Laravel 12 E-commerce Template

Production-ready fashion e-commerce platform built for **Bangladesh Facebook sellers**. Designed as a reusable white-label template with clean architecture, repository pattern, and service layer.

## Tech Stack

- **Laravel 12** · **Blade** · **Tailwind CSS 3** · **MySQL**
- Repository Pattern + Service Layer
- Mobile-first responsive design
- Shared hosting compatible (Apache + `.htaccess`)
- SEO: slugs, meta tags, sitemap, robots.txt
- Marketing: Facebook Pixel, GA4, GTM, TikTok Pixel

## Features

| Module | Capabilities |
|--------|-------------|
| **Auth** | Admin login, customer register/login, forgot password, profile |
| **Roles** | SUPER_ADMIN, ADMIN, STAFF, CUSTOMER |
| **Catalog** | Categories, brands, products, size/color variants, multi-image |
| **Commerce** | Cart, wishlist, COD checkout, coupons, order workflow |
| **Reviews** | Rating + comment with admin approval |
| **Settings** | Store branding, social links, marketing pixels |
| **Performance** | Image optimization, lazy loading, pagination, DB indexes |

## Requirements

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ (for asset build)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env
# DB_DATABASE=fashion_store
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Migrate & seed
php artisan migrate --seed
php artisan storage:link

# 5. Frontend assets
npm install
npm run build

# 6. Run locally
php artisan serve
```

Visit: `http://localhost:8000`  
Admin: `http://localhost:8000/admin/login`

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `ADMIN_EMAIL` from `.env` (default: `admin`) | `ADMIN_PASSWORD` (default: `admin`) |

## Shared Hosting Deployment

1. Upload all files to hosting (or deploy `public/` as document root)
2. Point domain document root to `/public`
3. Set `.env` with production values (`APP_DEBUG=false`)
4. Run via SSH or host panel:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Ensure `storage/` and `bootstrap/cache/` are writable (755/775)

## Architecture

```
app/
├── Enums/           # UserRole, OrderStatus, CouponType...
├── Models/          # Eloquent models + relationships
├── Repositories/
│   ├── Contracts/   # Interfaces
│   └── Eloquent/    # Implementations
├── Services/        # Business logic layer
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Frontend/
│   └── Requests/    # Form validation
└── Providers/       # DI bindings
```

## Customization for Clients

1. **Branding** — Admin → Settings → Store (name, logo, favicon, social links)
2. **Marketing & BD** — Admin → Marketing & BD (Facebook Pixel + CAPI, GA4, GTM, TikTok, shipping zones, SMS, social chat, SEO)
3. **Shipping** — Inside/outside Dhaka rates + free shipping limit in Marketing → Shipping
4. **Colors** — Edit `tailwind.config.js` brand colors
5. **Currency** — `CURRENCY_SYMBOL=৳` in `.env`

## Order Status Flow

```
Pending → Confirmed → Processing → Shipped → Delivered
    └────────────── Cancelled (from Pending/Confirmed/Processing)
```

## Coupon: WELCOME10

Seeded coupon: **10% off** orders above ৳500 (expires in 3 months).

## Theme Engine (Shopify-style)

### Available Themes
- `fashion-modern` — Bold contemporary (default)
- `fashion-classic` — Elegant serif styling
- `fashion-luxury` — Dark premium with gold accents
- `fashion-minimal` — Clean whitespace-focused

### Admin Theme Panel
Navigate to **Admin → Theme Engine**:
- **Customizer** — Switch theme, colors, fonts, logo, favicon
- **Homepage Builder** — Enable/disable sections, configure categories, products, flash sale
- **Hero Slides** — Manage slider banners
- **Footer Builder** — Social links, contact info
- **SEO** — Meta tags, OG image, JSON-LD schema

### Theme Structure
```
resources/views/themes/{theme-slug}/
  layouts/app.blade.php
  home.blade.php, product.blade.php, category.blade.php
  cart.blade.php, checkout.blade.php

public/themes/{theme-slug}/
  theme.css, theme.js  (versioned via asset_version)
```

### Helpers
```php
theme()->activeSlug();
theme_view('home', $data);
theme_asset('theme.css');
```

Run migrations after pull:
```bash
php artisan migrate --seed
```

## Marketing & Bangladesh E-commerce Module

Admin → **Marketing & BD**:

| Section | Features |
|---------|----------|
| **Pixels & CAPI** | Facebook Pixel, Conversion API (access token, dataset ID, test event code), GA4, GTM, TikTok |
| **Shipping (BD)** | Inside Dhaka / Outside Dhaka rates, free shipping threshold |
| **SMS** | Configurable API URL + key; order confirmed / shipped / delivered notifications |
| **Social Chat** | Floating WhatsApp, Messenger, Instagram, Telegram widgets |
| **SEO** | Default meta, robots.txt template, sitemap at `/sitemap.xml` |

### Tracking events (client + server)

- **Facebook**: PageView, ViewContent, AddToCart, InitiateCheckout, Purchase (Pixel + CAPI with `event_id`, `fbp`, `fbc`, hashed email/phone)
- **GA4**: page_view, view_item, add_to_cart, purchase
- **TikTok**: ViewContent, AddToCart, CompletePayment

### COD Checkout (Bangladesh)

Guest-friendly COD checkout with 8 divisions, district/area dropdowns, live shipping quote, order notes.

### Queue workers (CAPI + SMS)

```bash
# .env
QUEUE_CONNECTION=database

php artisan queue:work
```

## Courier Integration & Order Automation

Admin → **Courier & Automation** (`/admin/courier`):

| Courier | API integration |
|---------|-----------------|
| **Steadfast** | Api-Key + Secret-Key |
| **Pathao Courier** | OAuth client credentials |
| **RedX** | API access token |

### Features

- Per-courier API key, secret, base URL, enable/disable
- Default courier + **auto parcel** on order confirmed
- Order actions: Create Parcel, Sync Status, Print Invoice (A4 / 80mm thermal), Packing Slip (with QR), Shipping Label
- Extended order statuses: Parcel Created, Picked, In Transit, Returned
- **Scheduled sync** every 30 minutes (`SyncCourierParcelsJob`)
- Customer tracking at `/track-order` (phone + order number)
- Dashboard: today's orders/deliveries, delivery rate, courier performance
- Activity logs at `/admin/courier/activity`
- SMS: parcel created, shipped (with tracking), delivered, returned

### Scheduler (production)

```bash
# Add to crontab
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Installer Wizard (Shared Hosting)

Fresh installs auto-redirect to `/install` until setup is complete.

| Step | Action |
|------|--------|
| 1 | System requirements (PHP 8.2+, extensions, writable paths) |
| 2 | Database config + connection test → saves `.env` |
| 3 | Store name, email, phone, theme, currency, timezone |
| 4 | Super Admin account |
| 5 | Migrate, seed modules, storage link, cache, mark installed |

**After install:** `/install` redirects to `/admin/login`  
**Flag file:** `storage/app/installed` + `settings.app.installed`

### cPanel / Shared Hosting

1. Upload files, point domain to `/public`
2. Create MySQL database in cPanel
3. Visit `https://yourdomain.com/install`
4. Complete wizard (~2 minutes)
5. Run cron for scheduler + `php artisan queue:work` if using queues

## License System (Domain Lock)

Commercial deployments can lock each installation to a licensed domain.

| Feature | Details |
|---------|---------|
| Admin | `/admin/license` — generate, assign domain, edit, suspend, expire, search |
| Key format | `FBD-XXXX-XXXX-YYYY` (unique) |
| Middleware | `CheckLicense` — validates domain, status, expiry, HMAC signature |
| Helpers | `license()`, `license_valid()`, `licensed_domain()` |
| Invalid | `license/invalid` page — contact provider |
| Expired | `license/expired` page — renew license |

### Environment

```env
LICENSE_SKIP_LOCAL=true          # Skip check on localhost / .test / .local
LICENSE_SERVER_URL=              # Optional remote validation URL
LICENSE_SECRET=                  # Shared secret for HMAC signing
LICENSE_CACHE_TTL=86400          # Cache remote validation 24 hours
```

Set `LICENSE_SKIP_LOCAL=false` in production. Generate a license in admin, assign the production domain, and set status to **Active**.

### Remote validation (optional)

Point `LICENSE_SERVER_URL` to your license server. Client POSTs to `/validate` with signed payload. This project includes `POST /api/license/validate` if you host the server on another Fashion BD instance.

### Security

- License keys stored with `license_key_hash` (HMAC-SHA256)
- `verification_signature` prevents tampering with domain, expiry, or status
- Remote requests signed with `LICENSE_SECRET`

MIT — Free to use and resell to Bangladesh fashion businesses.
