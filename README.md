# MatrimonyCI - Application Mobile de Rencontre pour Mariage

## 📱 Présentation

MatrimonyCI est une application mobile révolutionnaire destinée aux personnes cherchant un partenaire pour le mariage en Côte d'Ivoire. Construite sur Laravel, elle intègre des paiements mobiles locaux (Orange Money, MTN Money, Moov Money, Wave) et des mécanismes de sécurité avancés.

## 🚀 Fonctionnalités Principales

✅ **Authentification Sécurisée**
- OAuth 2.0 (Google & Facebook)
- Vérification SMS du numéro de téléphone
- Acceptation obligatoire des conditions d'utilisation

✅ **Gestion de Profil**
- Création et modification détaillée
- Galerie multimédia (photos et vidéos)
- Modération automatique du contenu

✅ **Interactions Sociales**
- Système de likes avec détection des matches mutuels
- Commentaires modérés
- Signalement des utilisateurs frauduleux

✅ **Paiements Mobile Money**
- Orange Money
- MTN Money
- Moov Money
- Wave
- Abonnements mensuels/annuels

✅ **Sécurité et Modération**
- Blocage et bannissement automatique
- Historique des violations
- Journalisation complète des activités
- Protection contre les abus

## 🛠️ Stack Technique

- **Backend**: Laravel 10+
- **Base de Données**: MySQL 8+
- **Authentification**: Laravel Sanctum
- **Stockage**: AWS S3 ou local
- **Files & Jobs**: Laravel Queue
- **Modération**: AWS Rekognition (optionnel)
- **API**: RESTful JSON

## 📋 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL 8+
- Node.js (optionnel, pour les assets frontend)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/noasthy-wq/matrimonyci.git
cd matrimonyci

# 2. Installer les dépendances
composer install

# 3. Configuration
cp .env.example .env
php artisan key:generate

# 4. Base de données
php artisan migrate
php artisan db:seed

# 5. Démarrer le serveur
php artisan serve
```

## 🔐 Variables d'Environnement Essentielles

```env
APP_NAME=MatrimonyCI
APP_ENV=local
APP_KEY=base64:xxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_DATABASE=matrimonyci
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=xxxxx
GOOGLE_CLIENT_SECRET=xxxxx

FACEBOOK_CLIENT_ID=xxxxx
FACEBOOK_CLIENT_SECRET=xxxxx

ORANGE_MONEY_API_URL=https://api.orange.com/...
ORANGE_MONEY_CLIENT_ID=xxxxx
ORANGE_MONEY_CLIENT_SECRET=xxxxx

MTN_MONEY_API_URL=https://sandbox.momodeveloper.mtn.com
MTN_MONEY_PRIMARY_KEY=xxxxx

MOOV_MONEY_API_URL=https://sandbox.moov.io
MOOV_MONEY_API_KEY=xxxxx

WAVE_MONEY_API_URL=https://api.sandbox.wave.money
WAVE_MONEY_API_KEY=xxxxx
```

## 📚 Documentation API

### Authentification

#### Enregistrement
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+225XXXXXXXXXX",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "terms_accepted": true
}
```

#### Connexion
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePass123!"
}
```

### Profils

#### Lister les profils
```
GET /api/profiles?gender=femme&age_min=20&age_max=35&city=Abidjan
```

#### Voir un profil
```
GET /api/profiles/{id}
```

#### Créer/Mettre à jour un profil
```
POST /api/profiles
Authorization: Bearer {token}
Content-Type: application/json

{
  "gender": "homme",
  "age": 28,
  "religion": "Islam",
  "profession": "Ingénieur",
  "bio": "Cherche partenaire sérieuse...",
  "city": "Abidjan",
  "education": "Master"
}
```

### Likes

#### Ajouter un like
```
POST /api/likes
Authorization: Bearer {token}
Content-Type: application/json

{
  "profile_id": 1
}
```

#### Mes likes
```
GET /api/likes/my-likes
Authorization: Bearer {token}
```

### Paiements

#### Créer un paiement
```
POST /api/payments
Authorization: Bearer {token}
Content-Type: application/json

{
  "subscription_tier": "premium_monthly",
  "provider": "orange-money",
  "phone_number": "+225XXXXXXXXXX"
}
```

## 🧪 Tests

```bash
# Exécuter les tests
php artisan test

# Avec couverture
php artisan test --coverage
```

## 📊 Structure de Base de Données

```
users ──┬── profiles ──── media
        ├── likes
        ├── comments
        ├── payments
        ├── subscriptions
        ├── terms_acceptances
        ├── violations
        └── reports
```

## 🔧 Commandes Utiles

```bash
# Générer une clé API
php artisan key:generate

# Créer les tables
php artisan migrate

# Seed la base de données
php artisan db:seed

# Vérifier les suspensions expirées
php artisan moderation:check-suspensions

# Démarrer le queue worker (pour les jobs)
php artisan queue:work
```

## 📱 Conditions d'Utilisation

L'application inclut des clauses strictes de non-responsabilité et des mécanismes de modération pour prévenir les abus :

- Interdiction du harcèlement et de la fraude
- Suppression automatique de contenus inappropriés
- Bannissement des utilisateurs contrevenants
- Droit à l'oubli et suppression de données personnelles

## 🤝 Contribution

Pour contribuer au projet :

1. Forker le dépôt
2. Créer une branche feature (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -am 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## 📄 Licence

MIT License - voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

Pour toute question ou problème :
- 📧 Email: support@matrimonyci.ci
- 🐛 Issues: [GitHub Issues](https://github.com/noasthy-wq/matrimonyci/issues)

## 👨‍💻 Auteur

**noasthy-wq** - [GitHub Profile](https://github.com/noasthy-wq)

---

**Créé avec ❤️ pour la Côte d'Ivoire**
