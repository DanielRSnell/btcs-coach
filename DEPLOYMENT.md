# BTCS Coach - Simple Deployment Guide

## Quick Deploy (Recommended)

### Option 1: One-Command Deploy
```bash
curl -sSL https://raw.githubusercontent.com/DanielRSnell/btcs-coach/main/deploy.sh | bash
```

### Option 2: Manual Deploy
1. Download the compose file:
   ```bash
   curl -O https://raw.githubusercontent.com/DanielRSnell/btcs-coach/main/docker-compose.prod.yml
   ```

2. Start the application:
   ```bash
   docker compose -f docker-compose.prod.yml up -d
   ```

3. Check status:
   ```bash
   docker compose -f docker-compose.prod.yml ps
   ```

## Configuration

### Environment Variables
Set these before running:
```bash
export APP_URL=https://your-domain.com
export APP_ENV=production
```

### Custom Domain
For Coolify or similar platforms, just point to:
- **Image**: `ghcr.io/danielrsnell/btcs-coach:latest`
- **Port**: `80`
- **Environment**: Set `APP_URL` to your domain

## Automatic Updates

The image is automatically built and pushed to GitHub Container Registry on every commit to `main`. To update:

```bash
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

## Monitoring

### View Logs
```bash
docker compose -f docker-compose.prod.yml logs -f
```

### Check Health
```bash
docker compose -f docker-compose.prod.yml ps
curl -I http://localhost/
```

### Container Shell Access
```bash
docker compose -f docker-compose.prod.yml exec app sh
```

## Troubleshooting

### 403 Errors
1. Check container logs: `docker compose logs app`
2. Verify file permissions inside container
3. Ensure volumes are properly mounted

### Container Won't Start  
1. Check Docker daemon is running
2. Verify port 80 is available
3. Check compose file syntax

### Database Issues
The app uses SQLite stored in a Docker volume. To reset:
```bash
docker compose -f docker-compose.prod.yml down -v
docker compose -f docker-compose.prod.yml up -d
```

## Platform-Specific Deployment

### Coolify
1. Create new application
2. Set image to: `ghcr.io/danielrsnell/btcs-coach:latest`
3. Set environment variables
4. Deploy

### Railway/Render/Fly.io
1. Connect GitHub repository
2. Use `Dockerfile` for build
3. Set environment variables
4. Deploy

### Traditional VPS
1. Install Docker & Docker Compose
2. Use the deploy script above
3. Set up reverse proxy (Nginx/Caddy) if needed