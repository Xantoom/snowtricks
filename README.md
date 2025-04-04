# Snowtricks

> Snowtricks est un projet de formation de la formation Développeur PHP Symfony d'OpenClassrooms. Il s'agit d'un blog de figure de snowboard.

Étape du projet : 100% - soutenance validée

---

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les prérequis suivants sur votre système. Les instructions sont fournies pour **Windows, Mac et Linux**.

### 1. **Git**

Git est nécessaire pour cloner le projet depuis le dépôt.

#### Installation sur **Windows** :
1. Téléchargez Git depuis le site officiel : [https://git-scm.com/download/win](https://git-scm.com/download/win).
2. Exécutez le programme d'installation et suivez les instructions.
3. Une fois installé, ouvrez un terminal (Git Bash) et vérifiez l'installation avec :\
   ```git --version```

#### Installation sur **Mac** :
1. Ouvrez un terminal.
2. Installez Git via Homebrew (si Homebrew n'est pas installé, suivez les instructions sur [https://brew.sh/]
(https://brew.sh/) :\
   ```brew install git```
3. Vérifiez l'installation avec :\
   ```git --version```

#### Installation sur **Linux** (Ubuntu/Debian) :
1. Ouvrez un terminal.
2. Installez Git avec la commande :\
   ```sudo apt update```\
   ```sudo apt install git```
3. Vérifiez l'installation avec :\
   ```git --version```

---

### 2. **Docker et Make**

Docker et Make sont nécessaires pour lancer l'environnement de développement.

#### Installation sur **Windows** :
1. Téléchargez Docker Desktop depuis le site officiel : [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/).
2. Exécutez l'installeur et suivez les instructions.
3. Vérifiez l'installation avec :\
   ```docker --version```\
   ```docker-compose --version```
4. Installez Make pour Windows depuis le site officiel : [http://gnuwin32.sourceforge.net/packages/make.htm](http://gnuwin32.sourceforge.net/packages/make.htm).
5. Exécutez l'installeur et suivez les instructions.
6. Vérifiez l'installation avec :\
   ```make --version```

#### Installation sur **Mac** :
1. Téléchargez Docker Desktop depuis le site officiel : [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/).
2. Exécutez l'installeur et suivez les instructions.
3. Vérifiez l'installation avec :\
   ```docker --version```\
   ```docker-compose --version```
4. Installez Make via Homebrew :\
   ```brew install make```
5. Vérifiez l'installation avec :\
   ```make --version```

#### Installation sur **Linux** (Ubuntu/Debian) :
1. Installez Docker avec les commandes suivantes :\
   ```sudo apt update```\
   ```sudo apt install docker.io```\
   ```sudo systemctl start docker```\
   ```sudo systemctl enable docker```
2. Installez Docker Compose :\
   ```sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose```\
   ```sudo chmod +x /usr/local/bin/docker-compose```
3. Vérifiez l'installation avec :\
   ```docker --version```\
   ```docker-compose --version```
4. Installez Make avec la commande :\
   ```sudo apt install make```
5. Vérifiez l'installation avec :\
   ```make --version```
6. Ajoutez votre utilisateur au groupe Docker :\
   ```sudo usermod -aG docker $USER```
7. Déconnectez-vous et reconnectez-vous pour appliquer les changements.

---

## Installation du projet

1. Clonez le projet :\
   ```git clone <URL_DU_PROJET>```\
   ```cd <NOM_DU_PROJET>```
2. Démarrez le projet avec Docker :\
   ```make start```
3. Ouvrez votre navigateur à l'adresse : [http://localhost](http://localhost).

---

## Utilisation

- Vous pouvez voir toutes les commandes en faisant :\
  ```make```

- Une fois sur la page du site, vous pouvez vous inscrire ou vous connecter avec les identifiants suivants :
	- **Utilisateur** : `user@test.com` / `password`
	- **Auteur** : `editor@test.com` / `password`
	- **Administrateur** : `admin@test.com` / `password`
- Le site utilise le service de message gratuit **MAILERSEND** [https://mailersend.com/](https://mailersend.com/). Pour recevoir les e-mails, vous devez vous inscrire sur le site et obtenir une clé API. Ajoutez cette clé dans le fichier `.env` à la variable `MAILERSEND_API_KEY` et `MAILERSEND_MAIL_ADDRESS`. Une clé est déjà fourni dans le projet.

## Utilisation des commandes Doctrine

- Pour créer une nouvelle migration :\
  ```php bin/console make:migration```
- Pour exécuter les migrations :\
  ```php bin/console doctrine:migrations:migrate```
- Pour charger les fixtures :\
  ```php bin/console doctrine:fixtures:load --append```
- Pour supprimer la base de données :\
  ```php bin/console doctrine:database:drop --force```
- Pour mettre à jour la base de données :\
  ```php bin/console doctrine:schema:update --force```

Un Makefile est disponible dans le projet où toutes les commandes et leurs raccourcis sont disponibles.

---

## Arrêt

### Avec Docker et Make :
```make down```

---

## Auteur

* **Xavier Lauer** - [Xantoom](https://github.com/Xantoom)
