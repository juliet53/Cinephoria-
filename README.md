# 🎬 Cinéphoria

Cinéphoria est une plateforme multi-supports (Web, Mobile, Desktop) permettant :  
- 🎟️ La réservation de séances de cinéma  
- 🛠️ La gestion des incidents techniques  
- 🏢 L’administration complète des cinémas  

---

## 🚀 Déploiement local

### 🛠️ Prérequis
- PHP 8.2  
- Docker & Docker Compose  
- Symfony CLI  
- MySQL  
- MongoDB  
- Composer  
- API Platform  

---

### 📂 Cloner le dépôt Git
```bash
git clone https://github.com/juliet53/Cinephoria-.git
cd Cinephoria
```

---

### ⚙️ Créer le fichier `.env.local`
Créer un fichier `.env.local` à la racine du projet et y ajouter :  

```env
DATABASE_URL=mysql://cinephoria:cinephoria@db:3306/cinephoria
MONGODB_URL=mongodb://mongo:27017
MAILER_DSN=smtp://mailer:1025
```

---

## 🐳 Installation avec Docker (recommandé)

### ▶️ Lancer les conteneurs
```bash
docker compose up --build -d
```

---

### 🔍 Vérifier que tout tourne
```bash
docker ps
```

Tu dois voir :  
- `cinephoria_php`  
- `cinephoria_db`  
- `cinephoria_mongo`  
- `cinephoria_mailer`  

---

### 🗄️ Exécuter les migrations
```bash
docker exec -it cinephoria_php bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
exit
```

---

### 👤 Créer un utilisateur admin
```bash
docker exec -it cinephoria_db mysql -u cinephoria -pcinephoria cinephoria -e "
INSERT INTO user (email, password, roles) VALUES
('cine@demo.com', '$2y$10$WcRtCa1AM4oKoC8wcMhBG.WQQgB11hRW.lE3bxq1DvtV8b9QFfMSa', '[\"ROLE_ADMIN\"]');
"
```

👉 Identifiants de connexion :  
- **Email** : `cine@demo.com`  
- **Mot de passe** : `password`  

---

### 🌍 Accéder à l’application
- Symfony → [http://localhost:8080](http://localhost:8080)  
- Mailpit → [http://localhost:8025](http://localhost:8025)  
- MySQL → `localhost:3307`  
- MongoDB → `localhost:27018`  

---

## 🛠️ Installation manuelle (sans Docker)

### 📦 Installer les dépendances
```bash
composer install
```

---

### 🗄️ Créer la base avec le fichier `database.sql`
Connexion à MySQL :  
```bash
mysql -u votreUser -p
```

Création et import :  
```sql
CREATE DATABASE cinephoria;
USE cinephoria;
source database.sql;
```

Quitter MySQL :  
```bash
exit;
```

---

✅ Ton projet est prêt à tourner soit avec **Docker** (recommandé), soit avec **installation manuelle**.
