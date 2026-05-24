# Guide de Configuration MatrimonyCI

## 📋 Pré-requis

- PHP 8.1+
- MySQL 5.7+
- Redis (optionnel mais recommandé)
- Composer
- Node.js & npm (pour les assets)

## 🔧 Installation de Base

### 1. Cloner et Installer

```bash
# Cloner le dépôt
git clone https://github.com/noasthy-wq/matrimonyci.git
cd matrimonyci

# Installer les dépendances PHP
composer install

# Installer les dépendances Node
npm install
```

### 2. Configuration Environnement

```bash
# Copier .env.example en .env
cp .env.example .env

# Générer la clé app
php artisan key:generate
```

### 3. Configuration Base de Données

Modifiez `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matrimonyci
DB_USERNAME=root
DB_PASSWORD=your_password
```

Créez la base de données :

```bash
mysql -u root -p
> CREATE DATABASE matrimonyci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> EXIT;
```

### 4. Migrations et Seeders

```bash
# Exécuter les migrations
php artisan migrate

# (Optionnel) Remplir avec des données de test
php artisan db:seed
```

### 5. Démarrer l'Application

```bash
# Serveur Laravel
php artisan serve

# Dans un autre terminal : Queue worker
php artisan queue:work

# Dans un autre terminal : Assets en watch mode
npm run dev
```

## 🔐 Configuration OAuth

### Google OAuth

1. Aller à [Google Cloud Console](https://console.cloud.google.com/)
2. Créer un nouveau projet
3. Activer l'API Google+ 
4. Créer des identifiants OAuth 2.0 (Application Web)
5. Définir les URIs autorisées :
   - `http://localhost:8000/api/auth/google/callback`
   - `https://yourdomain.com/api/auth/google/callback`
6. Copier le Client ID et Secret dans `.env`

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
```

### Facebook OAuth

1. Aller à [Facebook Developers](https://developers.facebook.com/)
2. Créer une nouvelle app
3. Ajouter le produit "Login with Facebook"
4. Configuration :
   - URIs OAuth valides :
     - `http://localhost:8000/api/auth/facebook/callback`
     - `https://yourdomain.com/api/auth/facebook/callback`
5. Copier l'App ID et Secret

```env
FACEBOOK_CLIENT_ID=your-app-id
FACEBOOK_CLIENT_SECRET=your-app-secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/api/auth/facebook/callback
```

## 💳 Configuration Mobile Money

### Orange Money (Côte d'Ivoire)

1. S'inscrire sur [Orange Money Developer](https://developer.orange.com/)
2. Créer une application
3. Obtenir les credentials

```env
ORANGE_MONEY_API_URL=https://api.orange.com/orange-money-webservice/dev
ORANGE_MONEY_CLIENT_ID=your-client-id
ORANGE_MONEY_CLIENT_SECRET=your-client-secret
ORANGE_MONEY_MERCHANT_ID=your-merchant-id
```

### MTN Money (Côte d'Ivoire)

1. S'inscrire sur [MTN Open API](https://sandbox.momodeveloper.mtn.com/)
2. Créer une application
3. Générer les clés API

```env
MTN_MONEY_API_URL=https://sandbox.momodeveloper.mtn.com
MTN_MONEY_PRIMARY_KEY=your-primary-key
MTN_MONEY_SECONDARY_KEY=your-secondary-key
MTN_MONEY_USER_ID=your-user-id
```

### Moov Money (Afrique)

1. S'inscrire sur [Moov Sandbox](https://sandbox.moov.io/)
2. Créer une application
3. Obtenir l'API Key

```env
MOOV_MONEY_API_URL=https://sandbox.moov.io
MOOV_MONEY_API_KEY=your-api-key
MOOV_MONEY_ACCOUNT_ID=your-account-id
```

### Wave Money (International)

1. S'inscrire sur [Wave Developer](https://sandbox.wave.money/)
2. Créer une application
3. Obtenir l'API Key

```env
WAVE_MONEY_API_URL=https://api.sandbox.wave.money
WAVE_MONEY_API_KEY=your-api-key
WAVE_MONEY_MERCHANT_ID=your-merchant-id
```

## 📦 Configuration AWS S3

### Créer un bucket S3

1. Aller à [AWS S3 Console](https://s3.console.aws.amazon.com/)
2. Créer un nouveau bucket : `matrimonyci-media`
3. Configuration :
   - **Region** : `eu-west-1` (Irlande)
   - **Versioning** : Enabled
   - **Server-side encryption** : AES-256
   - **Public access** : Blocked (avec presigned URLs)

### Créer les credentials IAM

1. Aller à [IAM Console](https://console.aws.amazon.com/iam/)
2. Créer un nouvel utilisateur : `matrimonyci-app`
3. Attacher la politique inline :

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::matrimonyci-media",
                "arn:aws:s3:::matrimonyci-media/*"
            ]
        }
    ]
}
```

4. Générer les Access Keys

```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=matrimonyci-media
AWS_USE_PATH_STYLE_URLS=false
```

### AWS Rekognition (Modération)

Déjà configuré dans IAM. Activate le service :

```env
REKOGNITION_ENABLED=true
REKOGNITION_MIN_CONFIDENCE=80
```

## 📧 Configuration Email

### Mailtrap (Développement)

1. S'inscrire sur [Mailtrap.io](https://mailtrap.io/)
2. Créer une inbox
3. Copier les credentials SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@matrimonyci.ci
MAIL_FROM_NAME="MatrimonyCI"
```

### Sendgrid (Production)

```env
MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-sendgrid-api-key
MAIL_FROM_ADDRESS=noreply@matrimonyci.ci
MAIL_FROM_NAME="MatrimonyCI"
```

## 🔔 Configuration SMS (Twilio)

1. S'inscrire sur [Twilio](https://www.twilio.com/)
2. Obtenir un numéro de téléphone
3. Copier les credentials

```env
TWILIO_SID=your-account-sid
TWILIO_AUTH_TOKEN=your-auth-token
TWILIO_PHONE_NUMBER=+1234567890
```

## 🗄️ Configuration Redis

Pour le cache et les queues :

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 🚀 Déploiement

### Heroku

```bash
# Install Heroku CLI
# Login
heroku login

# Create app
heroku create matrimonyci

# Add buildpacks
heroku buildpacks:add heroku/php -a matrimonyci
heroku buildpacks:add heroku/nodejs -a matrimonyci

# Set env vars
heroku config:set APP_KEY=$(php artisan key:generate --show) -a matrimonyci
heroku config:set AWS_ACCESS_KEY_ID=... -a matrimonyci
# ... etc

# Deploy
git push heroku main
```

### DigitalOcean

```bash
# SSH into droplet
ssh root@your_ip

# Install dependencies
apt-get update && apt-get install -y php8.1 mysql-server nginx composer nodejs npm

# Clone and setup
git clone https://github.com/noasthy-wq/matrimonyci.git
cd matrimonyci
composer install --no-dev

# Configure nginx
# Copy matrimonyci.conf to /etc/nginx/sites-available/
sudo systemctl restart nginx

# SSL with Let's Encrypt
sudo certbot certonly --webroot -w /path/to/public -d api.matrimonyci.ci
```

### Docker

```dockerfile
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    mysql-client \
    git \
    curl \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY composer.json .
RUN composer install --no-dev

COPY . .

RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
```

```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "9000:9000"
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - DB_USERNAME=matrimonyci
      - DB_PASSWORD=secret
    depends_on:
      - db
      - redis
  
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
      - ./public:/app/public
    depends_on:
      - app
  
  db:
    image: mysql:8
    environment:
      - MYSQL_DATABASE=matrimonyci
      - MYSQL_ROOT_PASSWORD=secret
      - MYSQL_USER=matrimonyci
      - MYSQL_PASSWORD=secret
    volumes:
      - db_data:/var/lib/mysql
  
  redis:
    image: redis:alpine
    volumes:
      - redis_data:/data

volumes:
  db_data:
  redis_data:
```

## ✅ Checklist de Production

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS activé
- [ ] Database backups configurés
- [ ] Logs centralisés (CloudWatch/ELK)
- [ ] Monitoring & alertes
- [ ] Rate limiting activé
- [ ] CORS configuré correctement
- [ ] Secrets stockés de manière sécurisée
- [ ] Tests passent (`php artisan test`)
- [ ] Documentation à jour

---

Pour toute question, ouvrez une issue sur [GitHub](https://github.com/noasthy-wq/matrimonyci/issues).
