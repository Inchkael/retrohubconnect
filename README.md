# Espace Bien-être - Projet d'Examen 2026

**Auteur** : Mickaël Collings
**Date de remise** : 31/03/2026
**Branche GitHub** : `examen_2026`
**Lien GitHub** : https://github.com/Inchkael/projet-web-dynamique-collings.git

---

## 📋 Description du Projet

**Espace Bien-être** est une plateforme dédiée au bien-être, offrant des services de massage, yoga, méditation et coaching. Ce projet a été développé dans le cadre de l'examen 2026 et inclut :
- Une interface utilisateur moderne avec des effets de *glassmorphisme*.
- Un système de gestion des prestataires et des catégories de services.
- Des fonctionnalités de recherche.
- Une interface d'administration pour gérer les sliders, les catégories de services et les utilisateurs.

---

## 🛠 Prérequis

Pour exécuter ce projet, assurez-vous d'avoir installé :
- **Docker** (et Docker Compose)
- **PHP 8.4**
- **Composer**
- **Node.js** (et npm)
- **Git**

---

- **Chemin du projet** : Placez-vous à la racine du dossier du projet (ex: `cd /chemin/vers/Espace-Bien-Etre`).
- **Fichiers nécessaires** : Assurez-vous d'avoir les fichiers suivants dans le ZIP :


/docker-compose.yml

/Dockerfile

/.env.example

/database/seeders/


## 🚀 Installation et Lancement

### 1. Cloner le projet
git clone [lien_vers_votre_repo]
cd Espace-Bien-Etre
git checkout examen_2026

### 2. Builder l’image Docker
docker-compose build

### 3. Lancer les conteneurs
docker-compose up -d

### 4. Installer les dépendances PHP
docker-compose exec app composer install

### 5. Installer les dépendances npm
docker-compose exec app npm install
docker-compose exec app npm run dev

### 6. Configurer l’environnement
Copiez le fichier .env.example en .env et adaptez les variables d’environnement, notamment pour la base de données :

DB_CONNECTION=mariadb
DB_HOST=db
DB_PORT=3306
DB_DATABASE=prj_web
DB_USERNAME=admin
DB_PASSWORD=password

### 7. Générer la clé d’application
docker-compose exec app php artisan key\:generate

### 8. Lancer les migrations
docker-compose exec app php artisan migrate

### 9 . Jeux de Données

Exécuter ces requêtes pour alimenter les tables :
- commentaires_202603311655.sql
- images_202603311655.sql
- migrations_202603311655.sql
- service_categories_202603311654.sql
- service_category_user_202603311654.sql
- sliders_202603311653.sql
- users_202603311653.sql

Chaque prestataire est associé à une catégorie de service et situé dans une ville différente (ex : Liège, Bruxelles, Namur, Gand, Anvers).
Service du mois : La catégorie "Massage" est définie comme service du mois.

### 10 . Créer le lien symbolique pour le stockage
   docker-compose exec app php artisan storage\:link

### 11 . Les utilisateurs :

Utilisateur
utilisateur@isl-edu.be
utilisateur2026

Provider
provider@isl-edu.be
provider2026

Admin
admin@isl-edu.be
admin2026


### 13 . Accéder à l’Application

URL de l’application : http://localhost:8000
PhpMyAdmin : http://localhost:8080

Identifiants :

Serveur : db
Utilisateur : user
Mot de passe : password

🐳 Fichier Docker-Compose
Voici la configuration Docker utilisée pour le projet :

version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
    ports:
      - "8000:80"
    depends_on:
      - db
  db:
    image: mariadb:10.6
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: db
      MYSQL_USER: user
      MYSQL_PASSWORD: password
    ports:
      - "3306:3306"
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    depends_on:
      - db




