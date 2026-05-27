# MatrimonyCI - Application Mobile de Rencontre pour Mariage

🎊 **MatrimonyCI** est une application mobile de rencontre destinée aux personnes cherchant un partenaire pour le mariage en Côte d'Ivoire.

## 📋 Table des Matières

- [Aperçu](#aperçu)
- [Fonctionnalités Principales](#fonctionnalités-principales)
- [Architecture Technique](#architecture-technique)
- [Installation](#installation)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Déploiement](#déploiement)
- [Contribuer](#contribuer)
- [Licence](#licence)

## 🎯 Aperçu

MatrimonyCI est une plateforme complète permettant aux utilisateurs de :
- Créer des profils détaillés avec photos et vidéos
- Interagir via des likes et commentaires
- Souscrire à des abonnements premium via mobile money
- Signaler les utilisateurs abusifs
- Bénéficier d'une modération stricte pour prévenir les abus

## ✨ Fonctionnalités Principales

### 🔐 Authentification Sécurisée
- Inscription via Gmail ou Facebook (OAuth 2.0)
- Vérification du numéro de téléphone
- Acceptation obligatoire des conditions d'utilisation
- Tokens API avec Laravel Sanctum

### 👤 Gestion de Profil
- Création et modification de profil
- Champs personnalisés (âge, religion, profession, etc.)
- Galerie multimédia avec limite de taille et format
- Vérification du profil par modération

### 💬 Interactions Sociales
- Système de likes pour exprimer son intérêt
- Commentaires modérés sur les profils
- Détection des likes mutuels
- Signalement des utilisateurs abusifs

### 💳 Paiements via Mobile Money
- **Orange Money** (FCFA)
- **MTN Money** (FCFA)
- **Moov Money** (FCFA)
- **Wave** (International)
- Abonnements mensuels ou annuels
- Historique des transactions et reçus électroniques

### 🛡️ Modération et Sécurité
- Modération automatique des contenus
- Conditions d'utilisation strictes
- Blocage et bannissement des utilisateurs contrevenants
- Journalisation des activités
- Système de violations avec escalade

## 🏗️ Architecture Technique

### Stack Technologique
```
┌─────────────────────────────────────────┐
│     Mobile App (Flutter/React Native)   │
└────────────────┬────────────────────────┘
                 │ HTTP/REST API
                 ↓
┌─────────────────────────────────────────┐
│  Laravel Backend (PHP 8.1+)             │
│  - RESTful API                          │
│  - Laravel Sanctum (Auth)               │
│  - Queue Jobs                           │
└────────────────┬────────────────────────┘
                 │
    ┌────────────┼────────────┐
    ↓            ↓            ↓
┌────────┐  ┌────────┐  ┌─────────┐
│ MySQL  │  │ AWS S3 │  │ Redis   │
│ DB     │  │(Media) │  │(Cache)  │
└────────┘  └────────┘  └─────────┘
```

### Base de Données
- **MySQL** avec 10 tables principales
- Schéma relationnel optimisé
- Indexes sur les champs fréquemment recherchés

### Services Externes
- **OAuth** : Google et Facebook
- **Mobile Money** : 4 fournisseurs intégrés
- **AWS S3** : Stockage des médias
- **AWS Rekognition** : Modération d'images
- **Twilio** : Vérification SMS (optionnel)

## 🚀 Installation

### Prérequis
- PHP 8.1 ou supérieur
- MySQL 5.7 ou supérieur
- Composer
- Git

### Étapes d'Installation

1. **Cloner le dépôt**
```bash
git clone https://github.com/noasthy-wq/matrimonyci.git
cd matrimonyci
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Créer le fichier .env**
```bash
cp .env.example .env
```

4. **Générer la clé applicative**
```bash
php artisan key:generate
```

5. **Configurer la base de données**
Modifiez les variables dans `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matrimonyci
DB_USERNAME=root
DB_PASSWORD=
```

6. **Exécuter les migrations**
```bash
php artisan migrate
```

7. **Lancer les seeders (optionnel)**
```bash
php artisan db:seed
```

8. **Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible à `http://localhost:8000`

## ⚙️ Configuration

### Variables d'Environnement Essentielles

#### OAuth
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
FACEBOOK_CLIENT_ID=your-app-id
FACEBOOK_CLIENT_SECRET=your-app-secret
```

#### Mobile Money
```env
ORANGE_MONEY_CLIENT_ID=your-id
ORANGE_MONEY_CLIENT_SECRET=your-secret
MTN_MONEY_PRIMARY_KEY=your-key
WAVE_MONEY_API_KEY=your-key
```

#### AWS
```env
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_BUCKET=matrimonyci-media
AWS_REGION=eu-west-1
```

### Configuration des Fonctionnalités

Fichier : `config/matrimony.php`

```php
// Modération
'moderation' => [
    'enabled' => true,
    'service' => 'aws-rekognition',
    'min_confidence' => 80,
]

// Violations
'violations' => [
    'max_warnings' => 3,
    'suspension_duration_days' => 7,
    'max_violations_for_ban' => 5,
]

// Abonnements
'subscriptions' => [
    'tiers' => [
        'free' => [ ... ],
        'premium_monthly' => 5000, // FCFA
        'premium_annual' => 50000, // FCFA
    ]
]
```

## 📱 API Documentation

### Authentification

#### Enregistrement
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "Jean Dupont",
    "email": "jean@example.com",
    "phone": "+22512345678",
    "password": "SecurePassword123!",
    "password_confirmation": "SecurePassword123!",
    "terms_accepted": true
}
```

**Réponse** (201):
```json
{
    "message": "User registered successfully",
    "user": { ... },
    "token": "1|abc123..."
}
```

#### Connexion
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "jean@example.com",
    "password": "SecurePassword123!"
}
```

### Profils

#### Lister les profils
```http
GET /api/profiles?gender=femme&age_min=18&age_max=40&city=Abidjan
```

#### Afficher un profil
```http
GET /api/profiles/{id}
```

#### Créer un profil
```http
POST /api/profiles
Authorization: Bearer {token}
Content-Type: application/json

{
    "gender": "homme",
    "age": 28,
    "religion": "Islam",
    "profession": "Ingénieur",
    "bio": "À la recherche d'une femme sérieuse...",
    "city": "Abidjan"
}
```

### Likes

#### Ajouter un like
```http
POST /api/likes
Authorization: Bearer {token}
Content-Type: application/json

{
    "profile_id": 5
}
```

#### Retirer un like
```http
DELETE /api/likes/{profileId}
Authorization: Bearer {token}
```

#### Mes likes
```http
GET /api/likes/my-likes
Authorization: Bearer {token}
```

### Commentaires

#### Ajouter un commentaire
```http
POST /api/comments
Authorization: Bearer {token}
Content-Type: application/json

{
    "profile_id": 5,
    "content": "Bonjour, vous êtes intéressante..."
}
```

#### Lister les commentaires
```http
GET /api/comments/{profileId}
```

### Signalements

#### Signaler un utilisateur
```http
POST /api/reports
Authorization: Bearer {token}
Content-Type: application/json

{
    "reported_user_id": 10,
    "reason": "harassment",
    "description": "Cet utilisateur m'envoie des messages offensants..."
}
```

### Paiements

#### Créer un paiement
```http
POST /api/payments
Authorization: Bearer {token}
Content-Type: application/json

{
    "subscription_tier": "premium_monthly",
    "provider": "orange-money",
    "phone_number": "+22512345678"
}
```

#### Vérifier le statut du paiement
```http
GET /api/payments/{paymentId}/status
Authorization: Bearer {token}
```

## 🗄️ Structure des Bases de Données

### Users
```sql
- id (PRIMARY KEY)
- name
- email (UNIQUE)
- phone (UNIQUE)
- password
- provider (google, facebook)
- provider_id
- is_banned
- banned_at
- timestamps
```

### Profiles
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- gender
- age
- religion
- profession
- bio
- city
- education
- marital_status
- height
- complexion
- looking_for
- is_verified
- timestamps
```

### Media
```sql
- id (PRIMARY KEY)
- profile_id (FOREIGN KEY)
- path (S3 ou local)
- type (photo, video)
- file_size
- mime_type
- is_approved
- is_main
- rejection_reason
- timestamps
```

### Likes
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- profile_id (FOREIGN KEY)
- created_at (UNIQUE: user_id + profile_id)
```

### Comments
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- profile_id (FOREIGN KEY)
- content
- is_approved
- timestamps
```

### Payments
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- subscription_id (FOREIGN KEY)
- amount
- currency
- status (pending, completed, failed)
- provider (orange-money, mtn-money, etc.)
- transaction_id (UNIQUE)
- reference
- phone_number
- paid_at
- timestamps
```

### Subscriptions
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- tier (free, premium_monthly, premium_annual)
- status (active, cancelled, expired)
- starts_at
- expires_at
- timestamps
```

### Reports
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- reported_user_id (FOREIGN KEY)
- reason
- description
- status (pending, resolved, dismissed)
- resolved_at
- resolution_notes
- timestamps
```

### TermsAcceptances
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- version
- accepted_at
- ip_address
- user_agent
```

### Violations
```sql
- id (PRIMARY KEY)
- user_id (FOREIGN KEY)
- type (warning, suspension, ban)
- reason
- status (active, resolved, appealed)
- suspended_until
- details
- timestamps
```

## 🧪 Tests

### Exécuter les tests
```bash
php artisan test
```

### Avec couverture
```bash
php artisan test --coverage
```

### Tests spécifiques
```bash
php artisan test tests/Feature/AuthTest.php
```

## 📦 Déploiement

### Préparation
1. Configurer les variables d'environnement en production
2. Exécuter les migrations : `php artisan migrate --force`
3. Optimiser le cache : `php artisan config:cache`
4. Compiler les assets : `npm run build`

### Serveur
- **Environnement** : PHP 8.1+, MySQL 5.7+
- **Web Server** : Nginx ou Apache
- **SSL** : Certificat HTTPS obligatoire

### Nginx Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name api.matrimonyci.ci;
    
    root /path/to/matrimonyci/public;
    index index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## 🤝 Contribuer

### Process de Contribution
1. Fork le dépôt
2. Créer une branche feature (`git checkout -b feature/amazing-feature`)
3. Commiter vos changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

### Standards de Code
- PSR-12 pour PHP
- Laravel best practices
- Tests obligatoires pour les nouvelles fonctionnalités
- Documentation complète

## 📄 Licence

MatrimonyCI est sous licence **MIT**. Consultez le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

- **Email** : support@matrimonyci.ci
- **Issues** : [GitHub Issues](https://github.com/noasthy-wq/matrimonyci/issues)
- **Documentation** : [Wiki](https://github.com/noasthy-wq/matrimonyci/wiki)

---

**Créé avec ❤️ pour l'Afrique**
