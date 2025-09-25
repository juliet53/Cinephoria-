
# Cinéphoria

Cinéphoria est une plateforme de gestion de cinéma multi-supports (web, mobile, desktop) permettant la réservation de séances, la gestion des incidents techniques et l’administration complète des cinémas.

## Deployment

 ###  Configuration de l'Environnement

PHP 8.2 

Docker

Symfony CLI

Symfony 

mySQL

MongoDB

Api Platform

###  Cloner le dépot git 
Un dépot git public est associé au projet Arcadia. Pour le cloner en local, exécuter la commande :

```bash
git clone https://github.com/juliet53/Cinephoria-.git

````

###  Configurer les variables d'environnement
Dans le fichier .env.local , ajouter les variables d'environnement:
DATABASE_URL=

```bash
DATABASE_URL=
MONGODB_URL=
MAILER_DSN= 
````



###  Installer les dépendances du projet
Installer les dépendances:

```bash
composer install

````
###  Installer la base de donnée

Créer la base de donneés:

Le projet possede un fichier database.sql permettant de créer et remplir la base de données:

Connectez-vous a mysql:

```bash
mysql -u \votreUsername\ -p\votrePassword\
````


Utilisez le fichier database.sql:


```bash
source database.sql;

````


Une fois terminé, quittez MySQL :

```bash
exit;

````





