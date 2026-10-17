# BPMN Designer

Ce projet est une application Symfony 6 permettant de concevoir des diagrammes BPMN via une interface web de type glisser-déposer. L'utilisateur peut créer des organisations, des pools, des lanes, puis ajouter des éléments BPMN (tâches, gateways, événements, flux) et sauvegarder la structure dans une base de données.

## 1. Vue d'ensemble du projet

### Structure principale

- `src/Controller/` : contrôleurs Symfony qui exposent les routes et gèrent les requêtes HTTP / AJAX.
- `src/Entity/` : modèles Doctrine représentant les objets métier (Organisation, Pool, Lane, Flow, Task, Gateway, etc.).
- `src/Form/` : formulaires Symfony utilisés pour créer et modifier les éléments.
- `src/Repository/` : classes de requête Doctrine pour lire/filtrer les données.
- `templates/` : vues Twig affichées dans le navigateur.
- `public/assets/` : fichiers CSS, images et JavaScript utilisés pour l'interface graphique.
- `config/` : configuration Symfony, routes et services.
- `migrations/` : migrations Doctrine de la base de données.

### Fonctionnement métier

L'application est organisée autour d'un arbre de données suivant :

1. Organisation
2. Pool (zone de travail / swimlane principal)
3. Lane (voies internes à un pool)
4. Éléments BPMN (tâches, gateways, événements, activités)
5. Flow (liens entre éléments)

Le flux standard est le suivant :

- On crée une organisation depuis `/interface/{id}`.
- On ajoute ensuite des pools et des lanes associés à cette organisation.
- On peut ensuite glisser des éléments BPMN depuis la palette graphique.
- Les données sont enregistrées via des contrôleurs et des appels AJAX.
- Les informations sont relues depuis la base pour reconstruire la vue au chargement de la page.

## 2. Architecture technique

### Symfony / Doctrine

Le projet utilise :

- Symfony 6
- Doctrine ORM
- Twig
- Form component
- KnpPaginator
- PostgreSQL/MySQL/MariaDB selon la configuration de la base

Le cœur de la logique réside dans les contrôleurs qui :

- reçoivent les données de formulaire ou AJAX,
- récupèrent les objets liés via les repositories,
- les enregistrent avec Doctrine,
- renvoient des données JSON ou des vues Twig.

### Interface utilisateur

La partie front est principalement pilotée par :

- `templates/interface/index.html.twig`
- `public/assets/js/interface.js`
- `public/assets/css/interface.css`

Le fichier JavaScript gère :

- le drag and drop des objets BPMN,
- la création et le positionnement des éléments sur le plan de travail,
- les appels AJAX pour sauvegarder / récupérer les pools, lanes et flux,
- le rendu visuel du diagramme.

## 3. Routes principales

Voici les routes les plus importantes du projet :

- `/interface/{id}` : écran principal de gestion des organisations et du diagramme.
- `/groupe/index/{id}` : gestion des groupes d'une organisation.
- `/roles/index/{id}` : gestion des rôles d'une organisation.
- `/user/index/{id}` : gestion des utilisateurs d'une organisation.
- `/pool/get/{orgId}` : récupération des pools d'une organisation au format JSON.
- `/lane/get/{poolId}` : récupération des lanes d'un pool au format JSON.
- `/flow/get/{laneId}` : récupération des flux d'une lane.
- `/gateway/get/{laneId}` : récupération des gateways.
- `/task/get/{idLane}` : récupération des tâches.
- `/start_ev/get/{idLane}` : récupération des événements de début.
- `/stop_ev/get/{idLane}` : récupération des événements de fin.

## 4. Modèles principaux

### Organisation

La classe `Organisation` représente l'entité racine. Elle contient :

- l'identifiant,
- le nom,
- les relations vers les utilisateurs, rôles, groupes et pools.

### Pool

`Pool` représente une zone de travail BPMN. Elle est associée à une organisation et contient des lanes.

### Lane

`Lane` est une section interne d'un pool. Elle accueille les éléments BPMN.

### Flow / Gateway / Task / StartEv / StopEv / InterEv

Ce sont les éléments BPMN eux-mêmes :

- `Flow` : flux entre éléments,
- `Gateway` : point de décision ou de synchronisation,
- `Task` : tâche métier,
- `StartEv` / `StopEv` : événements de début / fin,
- `InterEv` : événement intermédiaire.

## 5. Déploiement et commandes utiles

### 5.1 Installation des dépendances

```bash
composer install
```

### 5.2 Configuration de la base de données

Le projet utilise une variable `DATABASE_URL` dans le fichier `.env`.

Important : la configuration par défaut du dépôt peut être incohérente selon la base réellement utilisée. Par exemple :

- `docker-compose.yml` configure un conteneur PostgreSQL,
- le fichier `.env` référence souvent une base MariaDB/MySQL.

Vérifiez donc bien la ligne suivante dans `.env` :

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/bpmn?serverVersion=mariadb-10.4.27&charset=utf8"
```

ou, si vous utilisez Docker PostgreSQL :

```env
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"
```

### 5.3 Lancer la base avec Docker

```bash
docker compose up -d database
```

### 5.4 Créer la base de données

```bash
php bin/console doctrine:database:create
```

### 5.5 Appliquer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### 5.6 Lancer l'application

Option 1 : Symfony CLI

```bash
symfony server:start
```

Option 2 : serveur PHP intégré

```bash
php -S localhost:8000 -t public
```

### 5.7 Vider le cache

```bash
php bin/console cache:clear
```

### 5.8 Lancer les tests

```bash
php bin/phpunit
```

ou, selon la configuration locale :

```bash
./vendor/bin/phpunit
```

## 6. Commandes de développement courantes

```bash
php bin/console doctrine:schema:update --force
php bin/console make:migration
php bin/console debug:router
php bin/console debug:autowiring
php bin/console lint:twig
```

## 7. Exemple de parcours utilisateur

1. Ouvrir l'application dans le navigateur.
2. Créer ou sélectionner une organisation.
3. Ajouter un pool et des lanes.
4. Glisser des éléments BPMN depuis la palette.
5. Relier les éléments via les flux.
6. Recharger la page pour vérifier que les données sont bien restituées depuis la base.

## 8. Point de vigilance

Le projet contient une logique de gestion de diagramme assez fortement couplée au JavaScript de la page et aux contrôleurs AJAX. Cela le rend très pratique pour un prototype de modélisation BPMN, mais il faut bien vérifier les relations Doctrine entre entités et la cohérence de la configuration de la base avant de travailler en production.

## 9. Résumé

Ce code est un mini éditeur BPMN réalisé avec Symfony. Il permet de gérer la structure d'une organisation, de dessiner un processus dans un espace graphique, et de sauvegarder cette structure en base de données. La logique est divisée entre :

- les entités Doctrine,
- les contrôleurs HTTP/AJAX,
- les vues Twig,
- le JavaScript de glisser-déposer.

C'est une base solide pour un outil de modélisation BPMN simple et personnalisable.
