# LMS (Learning Management System) on Laravel + Docker Compose

This is a complete Learning Management System built with Laravel framework and containerized using Docker Compose.

## 📋 Features

- **Laravel 10+** with PHP 8.2
- **MySQL 8.0** database
- **Redis** for caching and sessions
- **Nginx** web server
- **Laravel Scheduler** for background tasks
- Pre-configured Docker environment

## 🚀 Quick Start

### Prerequisites
- Docker (version 20+)
- Docker Compose (version 2.0+)

### Installation Steps

1. **Clone or navigate to the project directory:**
   ```bash
   cd lms-app
   ```

2. **Start all containers:**
   ```bash
   docker compose up -d --build
   ```

3. **Install Laravel dependencies:**
   ```bash
   docker compose exec app composer install
   ```

4. **Generate application key:**
   ```bash
   docker compose exec app php artisan key:generate
   ```

5. **Run database migrations:**
   ```bash
   docker compose exec app php artisan migrate
   ```

6. **Seed the database (optional):**
   ```bash
   docker compose exec app php artisan db:seed
   ```

7. **Access the application:**
   Open your browser and visit: `http://localhost:8080`

## 📁 Project Structure

```
lms-app/
├── docker/
│   ├── nginx/
│   │   └── default.conf      # Nginx configuration
│   └── php/
│       └── local.ini         # PHP configuration
├── docker-compose.yml        # Docker Compose configuration
├── Dockerfile                # PHP-FPM container image
├── .env                      # Environment variables
├── .env.example              # Example environment file
└── README.md                 # This file
```

## 🔧 Available Commands

### Using Makefile (if available):
```bash
make up          # Start containers
make down        # Stop containers
make restart     # Restart containers
make logs        # View logs
make shell       # Access app container
make migrate     # Run migrations
make seed        # Seed database
make test        # Run tests
```

### Direct Docker Compose commands:
```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f

# Access app container
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan <command>

# Run composer commands
docker compose exec app composer <command>
```

## 🗄️ Database Configuration

- **Host:** db
- **Port:** 3306
- **Database:** lms_database
- **Username:** lms_user
- **Password:** lms_password
- **Root Password:** root_password

## 🔌 Redis Configuration

- **Host:** redis
- **Port:** 6379

## 🛠️ Development Tips

### Clear caches:
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

### Run queue worker:
```bash
docker compose exec app php artisan queue:work
```

### Run tests:
```bash
docker compose exec app php artisan test
```

## 📦 LMS Core Features (To be implemented)

The following features can be added to build a complete LMS:

1. **User Management**
   - Students, Teachers, Admins roles
   - Authentication & Authorization

2. **Course Management**
   - Create/Edit courses
   - Course categories
   - Course enrollment

3. **Content Management**
   - Lessons, Videos, Documents
   - Quizzes and Assignments

4. **Progress Tracking**
   - Student progress
   - Grades and certificates

5. **Communication**
   - Announcements
   - Messaging system
   - Forums/Discussions

## 🔐 Security Notes

- Change default passwords in production
- Set `APP_DEBUG=false` in production
- Use strong `APP_KEY`
- Configure proper SSL/TLS for production
- Update `.env` with production values

## 📝 License

This project is open-source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

**Happy Learning! 🎓**
