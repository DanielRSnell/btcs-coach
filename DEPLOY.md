# 🚀 BTCS Coach - One-Click Deployment Guide

This guide provides everything you need to deploy BTCS Coach using Docker and Coolify with a single Dockerfile.

## 📋 What This Deployment Includes

✅ **Full Laravel Application** - Complete BTCS Coach platform  
✅ **React Frontend** - Built with TypeScript, ShadCN UI, and Framer Motion  
✅ **H.G. Fenton Branding** - Corporate colors and styling  
✅ **Pre-loaded Data** - Modules, profiles, action items, and test users  
✅ **Database Seeding** - All sample data automatically configured  
✅ **Production Ready** - Nginx + PHP-FPM optimized setup  

## 🐳 Coolify Deployment (Recommended)

### Option 1: Docker Compose (Recommended)
1. Log into your Coolify dashboard
2. Click **"New Resource"** → **"Service"** → **"Docker Compose"**
3. **Git Repository**: `git@github.com:DanielRSnell/btcs-coach.git`
4. **Branch**: `main`
5. **Docker Compose Path**: `./docker-compose.yml`

### Option 2: Single Dockerfile
1. Log into your Coolify dashboard
2. Click **"New Resource"** → **"Service"** → **"Docker Image"**
3. Choose **"Build from Dockerfile"**
4. **Git Repository**: `git@github.com:DanielRSnell/btcs-coach.git`
5. **Branch**: `main`
6. **Dockerfile Path**: `./Dockerfile`

### Step 3: Environment Variables
```env
APP_NAME="BTCS Coach"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

MAIL_MAILER=log
```

### Step 4: Port Configuration
- **Container Port**: `80`
- **Public Port**: `80` (or `443` for HTTPS)

### Step 5: Deploy
Click **"Deploy"** and Coolify will:
- Pull the repository
- Build the Docker image
- Install all dependencies
- Build frontend assets
- Run migrations
- Seed test data
- Start the application

## 🔧 Manual Docker Deployment

### Using Docker Compose (Recommended)
```bash
# Clone repository
git clone git@github.com:DanielRSnell/btcs-coach.git
cd btcs-coach

# Start with Docker Compose
docker-compose up -d

# View logs
docker-compose logs -f

# Stop
docker-compose down
```

### Using Plain Docker
```bash
# Build and run
docker build -t btcs-coach .
docker run -d -p 80:80 --name btcs-coach btcs-coach
```

### Benefits of Docker Compose:
- ✅ **Persistent Data** - Volumes for database and storage
- ✅ **Health Checks** - Automatic restart on failures
- ✅ **Environment Management** - Cleaner config
- ✅ **Production Ready** - Better for scaling

## 📦 What Gets Automatically Set Up

### 🧑‍💼 Pre-loaded Users
- **Admin**: `admin@btcs.com` / `password` (Admin role)
- **Main User**: `john@btcs.com` / `password` (Admin role)
- **Team Members**: 
  - `victor@umbral.ai` / `password` (Member)
  - `matt.putman@example.com` / `password` (Member)

### 📚 Pre-loaded Modules (H.G. Fenton Coaching System)
1. **Factual & Policy Retrieval** (Beginner, 5 min) - Company policies and procedures
2. **Personalization & Self-Awareness (Predictive Index)** (Intermediate, 10 min) - PI profile coaching
3. **Core Coaching Scenarios** (Advanced, 20 min) - Complex coaching using PI, SLII, and Courageous Conversations
4. **Performance & Growth Cycle** (Intermediate, 15 min) - Performance reviews and compensation planning
5. **Advanced Team & Culture Application** (Advanced, 25 min) - Team dynamics and culture integration

### 🎯 Pre-loaded Action Items
- **Completed**: Active listening practice, PI assessment, team one-on-ones, documentation updates
- **In Progress**: Competitor research analysis
- **Pending**: Communication style adaptation, team readiness assessment, leadership goals review

### 🏆 Achievement System
- First Steps badge
- Task Master milestone  
- Progress tracking

## 🔐 Security Notes

### Production Checklist
- [ ] Change `APP_KEY` to a secure random key
- [ ] Set `APP_DEBUG=false` 
- [ ] Configure proper domain in `APP_URL`
- [ ] Set up SSL/HTTPS
- [ ] Configure proper mail driver
- [ ] Set up backup strategy for SQLite database

### Environment Variables to Update
```env
# Generate new app key
APP_KEY=base64:$(php artisan key:generate --show)

# Your actual domain
APP_URL=https://btcs-coach.yourdomain.com

# Production mail configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 📊 Post-Deployment Access

### Admin Panel (Filament)
- URL: `https://your-domain.com/admin`
- Login: `john@btcs.com` / `password`
- Manage users, modules, and coaching sessions

### Main Application
- URL: `https://your-domain.com`
- Login: `john@btcs.com` / `password`
- Access dashboard, modules, and coaching interface

## 🔧 Customization After Deployment

### Adding New Modules
1. Access admin panel at `/admin`
2. Navigate to **Modules** section
3. Create new coaching/training modules
4. Assign to users as needed

### Managing Users
1. Go to **Users** in admin panel
2. Create new accounts
3. Assign roles (admin, member)
4. Configure PI assessments

### Updating Branding
All H.G. Fenton branding is pre-configured:
- **Primary Blue**: `#1e4a72`
- **Accent Orange**: `#e67e00`
- Logo and styling applied

## 🆘 Troubleshooting

### Container Won't Start
```bash
# Check logs
docker logs btcs-coach

# Common issues:
# - Database permissions
# - Missing environment variables
# - Port conflicts
```

### Database Issues
```bash
# Reset database (CAUTION: Deletes all data)
docker exec btcs-coach php artisan migrate:fresh --seed
docker exec btcs-coach php artisan db:seed --class=DashboardTestSeeder
```

### Permission Issues
```bash
# Fix storage permissions
docker exec btcs-coach chown -R www-data:www-data storage bootstrap/cache
docker exec btcs-coach chmod -R 755 storage bootstrap/cache
```

## 📞 Support

For deployment issues:
1. Check container logs
2. Verify environment variables
3. Ensure proper port configuration
4. Review Coolify service status

---

**🎉 That's it!** Your BTCS Coach platform should be live with all sample data, ready for H.G. Fenton's coaching and training needs.