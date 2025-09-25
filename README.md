#  Cinéphoria

Cinéphoria est une plateforme multi-supports (Web, Mobile, Desktop) permettant :  
-  La réservation de séances de cinéma  
-  La gestion des incidents techniques  
-  L’administration complète des cinémas  

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

###  Cloner le dépôt Git
```bash
git clone https://github.com/juliet53/Cinephoria-.git
cd Cinephoria

###  Créer le .env
```bash
DATABASE_URL=mysql://cinephoria:cinephoria@db:3306/cinephoria
MONGODB_URL=mongodb://mongo:27017
MAILER_DSN=smtp://mailer:1025
