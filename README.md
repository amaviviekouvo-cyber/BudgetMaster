# 💰 BudgetMaster

> Application web de gestion et de suivi des finances personnelles.

## 📌 Présentation

**BudgetMaster** est une application web développée dans le cadre de mon apprentissage du développement logiciel.

Elle permet de suivre ses revenus et dépenses, de fixer des budgets mensuels, d'épargner pour des objectifs et de suivre ses investissements, le tout avec des statistiques claires.

## ✨ Fonctionnalités

| Module | Ce qu'il permet |
|---|---|
| 💸 **Transactions** | Revenus et dépenses, recherche, filtres par type et par mois, totaux, **export CSV** (compatible Excel) |
| 📂 **Catégories** | Catégories personnelles de revenus ou de dépenses (créées automatiquement à l'inscription) |
| 💰 **Budgets** | Un budget par mois, barre de progression, reste à vivre, **alerte à 80 % et en cas de dépassement** |
| 🎯 **Objectifs** | Montant cible, échéance, **épargne mensuelle conseillée**, notification quand l'objectif est atteint |
| 🐷 **Épargne** | Versements libres ou liés à un objectif (l'objectif progresse automatiquement) |
| 📈 **Investissements** | Montant investi, valeur actuelle, plus/moins-value et performance en % |
| 📊 **Statistiques** | 6 ou 12 mois : revenus/dépenses, évolution du solde, répartition par catégorie, taux d'épargne |
| 🔔 **Notifications** | Alertes de budget, objectifs atteints |
| ⚙️ **Paramètres** | Devise (€, $, FCFA), thème clair/sombre, profil, changement de mot de passe |

## 🔒 Sécurité

- Mots de passe hachés (`password_hash`), 8 caractères minimum
- Requêtes préparées PDO (protection contre les injections SQL)
- Jetons **CSRF** sur tous les formulaires, suppressions uniquement en POST
- Échappement systématique des sorties (protection XSS)
- Chaque donnée est filtrée par utilisateur (impossible d'accéder aux données d'un autre compte)
- Cookie de session `HttpOnly` / `SameSite`, régénération de l'identifiant à la connexion

## 🛠️ Technologies

PHP 8 · MySQL / MariaDB · HTML5 · CSS3 · JavaScript · Bootstrap 5 · Chart.js · Docker

## 📂 Structure du projet

```
BudgetMaster/
├── auth/            Connexion, inscription, déconnexion
├── dashboard/       Tableau de bord
├── transactions/    CRUD + filtres + export CSV
├── categories/      budgets/  goals/  savings/  investments/
├── statistics/      Graphiques
├── notifications/   settings/
├── includes/        bootstrap.php (session + BDD), functions.php, gabarits (header, sidebar, navbar, footer)
├── config/          database.php (configurable par variables d'environnement)
├── database/        budgetmaster.sql (schéma complet)
├── assets/          CSS, JS, images
├── Dockerfile       Image PHP 8.2 + Apache
└── docker-compose.yml
```

## 🚀 Installation

### Option 1 — XAMPP

1. Cloner le projet dans `C:\xampp\htdocs\` :
   ```bash
   git clone https://github.com/amaviviekouvo-cyber/BudgetMaster.git
   ```
2. Démarrer **Apache** et **MySQL** depuis le panneau XAMPP.
3. Importer `database/budgetmaster.sql` dans phpMyAdmin (il crée la base `budgetmaster`).
4. Ouvrir <http://localhost/BudgetMaster/>.

### Option 2 — Docker

```bash
docker compose up -d --build
```

L'application est disponible sur <http://localhost:8080> ; la base de données est créée et initialisée automatiquement.

## ☁️ Déploiement

L'application lit sa configuration dans les variables d'environnement suivantes (valeurs par défaut = XAMPP) :

| Variable | Défaut |
|---|---|
| `DB_HOST` | `localhost` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `budgetmaster` |
| `DB_USER` | `root` |
| `DB_PASS` | *(vide)* |

Elle peut donc être déployée sur tout hébergeur acceptant un `Dockerfile` (Railway, Render, Fly.io…) avec une base MySQL, ou sur un hébergement PHP mutualisé (en important `database/budgetmaster.sql` et en renseignant les variables ci-dessus).

## 📚 Ce que ce projet m'a permis d'apprendre

- la création d'une interface web ;
- la logique côté client et côté serveur ;
- la gestion d'une base de données relationnelle ;
- la sécurité des applications web ;
- l'organisation d'un projet, Git et GitHub ;
- la conteneurisation avec Docker.

## 👩🏾‍💻 Projet

Designed & Developed by **EKOUVO Vivi** 💖
