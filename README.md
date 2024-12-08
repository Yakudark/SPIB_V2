# SPIB - Système de Gestion des Entretiens

## Installation en local (Intranet)

1. Cloner le projet dans le dossier www de WAMP :
   ```bash
   cd c:/wamp64/www/JS
   git clone [URL_DU_REPO] SPIB
   ```

2. Créer la base de données :
   - Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
   - Créer une nouvelle base de données nommée "spib_gestion"
   - Importer le fichier `database/init.sql`

3. Configurer l'accès à la base de données :
   - Ouvrir le fichier `config/database.php`
   - Modifier les paramètres de connexion si nécessaire (par défaut : root sans mot de passe)

4. Installation des dépendances :
   ```bash
   npm install
   ```

5. Compiler les styles CSS :
   ```bash
   node build.js
   ```

6. Configuration du serveur Web :
   - Assurez-vous que le module rewrite d'Apache est activé
   - Les fichiers .htaccess sont déjà configurés pour le routage

7. Accéder à l'application :
   - Ouvrir http://localhost/JS/SPIB dans un navigateur

## Structure du projet

```
SPIB/
├── api/              # Point d'entrée de l'API
├── config/           # Fichiers de configuration
├── controllers/      # Contrôleurs
├── database/         # Scripts SQL
├── models/           # Modèles
├── public/          # Fichiers publics
│   ├── css/         # Styles compilés
│   ├── js/          # Scripts JavaScript
│   └── lib/         # Bibliothèques externes
└── src/             # Sources
    └── css/         # Styles source Tailwind
```

## Fonctionnalités

- Gestion des employés
- Suivi des entretiens
- Interface drag & drop
- Gestion des services et départements
- Tableau de bord avec statistiques

## Notes importantes

- L'application est conçue pour fonctionner en intranet
- Toutes les dépendances sont incluses localement
- Pas de dépendance aux CDN externes
- Compatible avec les navigateurs modernes (Chrome, Firefox, Edge)
