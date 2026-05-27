# Architecture MatrimonyCI

## Vue d'Ensemble de l'Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT LAYER (Mobile App)                 │
│              Flutter / React Native Application              │
└────────────────────────┬────────────────────────────────────┘
                         │
                    HTTPS/REST API
                         │
┌────────────────────────▼────────────────────────────────────┐
│              LARAVEL BACKEND API LAYER                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌────────────┐  ┌──────────┐  ┌──────────┐  ┌────────────┐ │
│  │   Auth     │  │ Profile  │  │  Like &  │  │  Payment   │ │
│  │ Controller │  │Controller│  │ Comment  │  │ Controller │ │
│  │            │  │          │  │Controller│  │            │ │
│  └────────────┘  └──────────┘  └──────────┘  └────────────┘ │
│                                                               │
│  ┌────────────┐  ┌──────────┐  ┌──────────┐  ┌────────────┐ │
│  │  Report    │  │ Subscription│  │ Media  │  │ Moderation │ │
│  │ Controller │  │ Controller  │  │Handler │  │ Service    │ │
│  └────────────┘  └──────────┘  └──────────┘  └────────────┘ │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │              MIDDLEWARE LAYER                            │ │
│  │  ┌──────────────┐  ┌──────────┐  ┌────────────────────┐ │ │
│  │  │ Sanctum Auth │  │Verify    │  │ Check Subscription │ │ │
│  │  │              │  │ Terms    │  │                    │ │ │
│  │  └──────────────┘  └──────────┘  └────────────────────┘ │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
└────────────────────────┬────────────────────────────────────┘
                         │
        ┌────────────────┼────────────────┬──────────────┐
        │                │                │              │
        ▼                ▼                ▼              ▼
┌─────────────┐  ┌──────────────┐  ┌──────────┐  ┌──────────────┐
│   MySQL     │  │  AWS S3      │  │  Redis   │  │ Job Queue    │
│  Database   │  │  (Media)     │  │  (Cache) │  │ (Processing) │
│             │  │              │  │          │  │              │
│ • Users     │  │ • Photos     │  │ • Auth   │  │ • Moderation │
│ • Profiles  │  │ • Videos     │  │ • Sessions│  │ • Payments   │
│ • Likes     │  │ • Thumbnails │  │ • Cache  │  │ • Emails     │
│ • Comments  │  │              │  │          │  │              │
│ • Payments  │  │              │  │          │  │              │
│ • Reports   │  │              │  │          │  │              │
└─────────────┘  └──────────────┘  └──────────┘  └──────────────┘
        │                                              │
        └──────────────────┬───────────────────────────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌─────────────────┐ ┌──────────────┐ ┌────────────────┐
│  OAuth Providers│ │Mobile Money  │ │External APIs   │
│  • Google       │ │  • Orange    │ │ • AWS Rekognition
│  • Facebook     │ │  • MTN       │ │ • Twilio SMS   │
│                 │ │  • Moov      │ │ • Email Service│
│                 │ │  • Wave      │ │                │
└─────────────────┘ └──────────────┘ └────────────────┘
```

## Flux de Données

### 1. Authentification Utilisateur
```
Client → POST /api/auth/register
        ↓
   AuthController::register
        ↓
   Validate Input (CreateProfileRequest)
        ↓
   Create User (Hash Password)
        ↓
   Create TermsAcceptance Record
        ↓
   Create Free Subscription
        ↓
   Generate Sanctum Token
        ↓
 Client ← Return User + Token
```

### 2. Création de Profil
```
Client → POST /api/profiles (with token)
        ↓
   Sanctum Middleware (Auth)
        ↓
   VerifyTermsAccepted Middleware
        ↓
   ProfileController::store
        ↓
   Validate Input
        ↓
   Create/Update Profile
        ↓
   MySQL (Persist)
        ↓
   Client ← Return Profile
```

### 3. Upload de Média
```
Client → POST /api/media (photo/video)
        ↓
   Validate (File size, type, dimensions)
        ↓
   Upload to AWS S3
        ↓
   Create Media Record (MySQL)
        ↓
   Queue Moderation Job (Redis)
        ↓
   ModerateProfileMedia Job
        ↓
   AWS Rekognition Analysis
        ↓
   Update is_approved Flag
        ↓
   Client ← Return Media URL
```

### 4. Système de Paiement
```
Client → POST /api/payments
        ↓
   Validate Subscription & Provider
        ↓
   Create Payment Record (status: pending)
        ↓
   PaymentService::initiate()
        ↓
   Route to Provider (Orange/MTN/Moov/Wave)
        ↓
   Provider API Call
        ↓
   Return Transaction ID
        ↓
   User Enters PIN on Phone
        ↓
   Provider Webhook Callback
        ↓
   Verify & Update Payment Status
        ↓
   Create Subscription (if successful)
        ↓
   Send Confirmation Email
        ↓
   Client ← Return Status
```

### 5. Modération de Contenu
```
User Posts Content (Comment/Photo)
        ↓
   Create Record (is_approved: false)
        ↓
   Queue Moderation Job
        ↓
   Async Processing (Redis Queue)
        ↓
   Content Analysis
        ├─ Text: Check Banned Words
        ├─ Image: AWS Rekognition
        └─ Video: Extract Frames + Analyze
        ↓
   Decision
   ├─ ✅ Approve → Update is_approved
   ├─ ❌ Reject → Create Violation Record
   └─ 🚩 Flag for Manual Review
        ↓
   If Violations Threshold Exceeded
        ↓
   Apply Sanctions
   ├─ Warning
   ├─ Temporary Suspension
   └─ Permanent Ban
```

## Patterns & Best Practices

### Service Pattern
```php
// Services isolent la logique métier complexe
class PaymentService
{
    public function initiate(Payment $payment): array { }
    public function checkStatus(Payment $payment): array { }
}

// Utilisé dans les contrôleurs
public function __construct(PaymentService $paymentService)
{
    $this->paymentService = $paymentService;
}
```

### Repository Pattern (optionnel)
```php
class UserRepository
{
    public function findActive() { }
    public function findBanned() { }
    public function getByEmail($email) { }
}
```

### Job/Queue Pattern
```php
// Tâches asynchrones pour les opérations longues
Dispatch(new ModerateProfileMedia($media));
Dispatch(new ProcessPayment($payment));
```

### Middleware Pattern
```php
// Contrôle d'accès et validation
Route::middleware('auth:sanctum', 'verify.terms')->group(...);
```

## Scalabilité

### Horizontal Scaling
1. **Base de Données** : Master-Slave Replication
2. **Cache** : Redis Cluster
3. **Files d'Attente** : RabbitMQ ou Beanstalkd
4. **Stockage** : AWS S3 (illimité)

### Load Balancing
```
Client Requests
        ↓
Load Balancer (Nginx)
        ↓
    ┌───┴───┐
    ▼       ▼
 Server1 Server2 ... ServerN
    │       │       │
    └───┬───┘       │
        └─────────┬─┘
                  ▼
            Shared MySQL
            Shared Redis
            Shared S3
```

## Sécurité

### Layers de Sécurité
1. **Transport** : HTTPS/TLS 1.3
2. **Authentication** : OAuth 2.0 + Sanctum
3. **Authorization** : Policies & Middleware
4. **Input Validation** : FormRequest classes
5. **SQL Injection** : Prepared Statements (Eloquent)
6. **XSS Protection** : JSON responses
7. **CSRF** : Token validation (API safe)
8. **Rate Limiting** : Throttle middleware
9. **Data Encryption** : AES-256 pour données sensibles
10. **Logging** : Audit trail pour actions critiques

## Performance

### Optimisations
1. **Database** :
   - Indexes sur colonnes fréquemment recherchées
   - Eager loading (with())
   - Query caching

2. **API** :
   - Pagination par défaut
   - Response compression
   - Caching headers

3. **Storage** :
   - S3 CDN pour médias
   - Image thumbnails
   - Video transcoding

4. **Queue** :
   - Modération asynchrone
   - Emails en background
   - Webhooks retry logic

## Monitoring & Logging

### Logs
- `storage/logs/laravel.log` : Application logs
- `storage/logs/payment.log` : Payment transactions
- `storage/logs/moderation.log` : Content moderation

### Metrics
- Active users
- Payment success rate
- API response times
- Error rates
- Queue job counts

---

Pour plus de détails, consultez la [Documentation API](API.md).
