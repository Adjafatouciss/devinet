# DeviNet – Jeu de devinettes | Guessing game

Projet final du cours **420-DW3-AS Développement d'applications web côté serveur 1** – Collège LaSalle
Autrice : **Adja Fatou CISS**

🔗 **Démo en ligne :** <https://devinet-adja.free.nf>
👤 **Compte de démonstration :** `sonic12345` / `hellomontreal`

DeviNet est une application web PHP / MySQL dans laquelle un utilisateur inscrit
doit deviner 5 nombres distincts (de 0 à 12) générés au hasard par le serveur,
grâce à une interface de glisser-déposer.

---

## 1. Fonctionnalités

- **Inscription** avec vérification du nom d'utilisateur **en temps réel (AJAX)** et
  contrôle des 2 mots de passe.
- **Connexion** sécurisée (mots de passe chiffrés, nom d'utilisateur identique au caractère près).
- **Jeu** : glisser-déposer de 5 nombres, résultat détaillé, appréciation du joueur
  et score cumulé pendant la session.
- **Déconnexion** et protection des pages réservées aux utilisateurs connectés.
- Création **automatique** de la base de données, de la table et des comptes de test.

## 2. Prérequis

- PHP **8.0 ou plus récent** (utilise l'expression `match` et la promotion des propriétés du constructeur)
- MySQL ou MariaDB
- Un serveur local comme **WampServer**, XAMPP ou MAMP
- L'extension PHP `pdo_mysql` (activée par défaut dans WAMP et XAMPP)

## 3. Installation en local

1. Copier le dossier `devinet-jeu-game` dans le dossier web
   (WampServer : `C:\wamp64\www\` – XAMPP : `C:\xampp\htdocs\`).
2. Démarrer WampServer (icône verte), ou Apache + MySQL dans le panneau XAMPP.
3. Si votre utilisateur, mot de passe ou port MySQL n'est pas `root` / *(vide)* / `3306`,
   modifier `config/config.php`.
4. Ouvrir <http://localhost/devinet-jeu-game/> dans un navigateur.

La base `jeu_devinette`, la table `compte_utilisateur` et les 3 comptes de test sont
**créés automatiquement** lors du premier accès à l'application. Aucun script SQL
n'est à importer.

### Mise en ligne (ex. InfinityFree)

Les hébergeurs mutualisés interdisent `CREATE DATABASE`. Il faut créer la base dans
le panneau de l'hébergeur, puis copier `config/config.local.example.php` en
`config/config.local.php` sur le serveur et y indiquer l'hôte, l'utilisateur, le mot
de passe et le nom de la base, avec `DB_CREATE_DATABASE` à `false`. La table et les
comptes de test sont quand même créés automatiquement. Le fichier
`config.local.php` est exclu de Git (`.gitignore`) car il contient le mot de passe.

## 4. Comptes de test

| Nom d'utilisateur | Mot de passe    |
|-------------------|-----------------|
| `sonic12345`      | `hellomontreal` |
| `asterix2023`     | `helloquebec`   |
| `pokemon527`      | `hellocanada`   |

## 5. Utilisation

1. **S'inscrire** (formulaire d'inscription) ou **se connecter** avec un compte de test.
2. Dans le formulaire de jeu, glisser-déposer 5 nombres dans les 5 cases
   (on peut aussi cliquer sur un nombre pour le placer, puis recliquer pour le retirer).
3. Cliquer sur **PLAY** : les nombres générés, les nombres choisis, le résultat et
   l'appréciation s'affichent, et le score est mis à jour.
4. Cliquer sur **LOG OUT** pour terminer la session.

### Score
`points gagnés / points possibles` – chaque partie ajoute 1 point par nombre deviné
et 5 points possibles. Exemple : 2/5 → 6/10 → 6/15.

### Mode test (pour montrer le scénario 1)
Deviner les 5 nombres n'arrive qu'environ une fois sur 1 287 parties. Pour le
démontrer, mettre `GAME_TEST_MODE = true` dans `config/config.php` : le jeu
« génère » alors toujours `1, 0, 3, 2, 4`. **Le remettre à `false` ensuite.**

## 6. Liste des fichiers

```
devinet-jeu-game/
├── index.php                       Point d'entrée : redirige vers la connexion ou le jeu
├── login-form.php                  Front-end : formulaire de connexion (+ création auto de la BD)
├── login-form-handling.php         Back-end : vérifie les informations de connexion
├── registration-form.php           Front-end : formulaire d'inscription
├── registration-form-handling.php  Back-end : valide et enregistre un nouveau compte
├── check-username.php              Back-end : réponse AJAX (JSON) pour le nom d'utilisateur
├── game-form.php                   Front-end : formulaire du jeu
├── game-form-handling.php          Back-end : joue une partie et met à jour le score
├── logout.php                      Détruit la session et revient à la connexion
├── config/
│   ├── config.php                  Constantes : BD, nom de l'application, règles du jeu
│   └── config.local.example.php    Modèle de configuration pour l'hébergement en ligne
├── classes/
│   ├── Database.php                Connexion PDO + création auto de la BD, table et données
│   ├── DatabaseException.php       Exception personnalisée pour les erreurs de BD
│   ├── UserManager.php             Comptes : existe / créer / vérifier la connexion
│   └── GuessingGame.php            Règles du jeu : validation, génération, résultat (match)
├── includes/
│   └── functions.php               Fonctions réutilisables : session, messages, header, footer
├── js/
│   ├── registration.js             AJAX (XMLHttpRequest) + vérification des mots de passe
│   └── game.js                     Glisser-déposer + horloge en direct
├── css/
│   └── style.css                   Styles de toutes les pages
└── README.md                       Ce fichier
```

## 7. Choix techniques

- **Séparation front-end / back-end** : chaque formulaire a sa page d'affichage et sa
  page de traitement. Les pages de traitement n'affichent jamais de HTML : elles
  valident, enregistrent les messages en session puis redirigent (modèle
  Post/Redirect/Get). Tous les messages s'affichent donc dans une page front-end.
- **Fonctions et classes** : le HTML répété (header, footer, messages, champs) est
  généré par des fonctions dans `includes/functions.php` ; la logique de la base de
  données et du jeu est dans des classes.
- **try / catch** : chaque opération MySQL est dans un `try/catch`. Une
  `PDOException` est transformée en `DatabaseException` avec un message clair
  affiché dans la page (par exemple quand MySQL est arrêté).
- **Sécurité** : requêtes préparées (pas d'injection SQL), `password_hash()` /
  `password_verify()`, `htmlspecialchars()` sur chaque valeur affichée (pas de XSS),
  `session_regenerate_id()` à la connexion, méthode POST pour chaque action.
- **Connexion exacte** : le nom d'utilisateur doit être identique, majuscules comprises.
- **Composants PHP non vus en classe** : expression `match` (`GuessingGame.php`,
  `game-form.php`), promotion des propriétés du constructeur, types union
  (`string|int`), type `mixed`, syntaxe heredoc.

## 8. Limites connues / dépannage

- *« Unable to connect to the database »* : MySQL n'est pas démarré, ou les
  paramètres de `config/config.php` sont incorrects (MAMP utilise le port 8889 et
  le mot de passe `root` ; certaines installations WAMP utilisent le port 3307).
- Le score est conservé dans la session : il recommence à 0/0 à chaque connexion.
- L'horloge en direct utilise l'heure de l'ordinateur du visiteur ; la première
  valeur affichée vient du serveur (fuseau America/Toronto).
- L'interface du jeu est en anglais, comme demandé dans l'énoncé du projet.

## 9. Crédits

Énoncé du projet et données de test : Patrick Saint-Louis, Collège LaSalle.
Commentaires relus avec l'aide d'un assistant IA, comme le permettait l'énoncé.

© 2026 Adja Fatou CISS – Tous droits réservés.
