# Projet technique : Gestion des groupes musculaires (Fitcouch)

Petite application de gestion (CRUD) qui prolonge la **Session 2** (groupes musculaires) en démontrant :

1. la **séparation des responsabilités** en PHP (Entité / Gestionnaire / Contrôleur) ;
2. une interface **Tailwind mobile-first** avec feedbacks asynchrones (spinners, toasts, états d'erreur) ;
3. un suivi de travail par **Issues GitHub**.

## Architecture

| Couche | Fichier | Responsabilité unique |
|---|---|---|
| Entité | `backend/src/Entity/GroupeMusculaire.php` | Données + règles de validation. Ignore HTTP et stockage. |
| Gestionnaire | `backend/src/Manager/GroupeMusculaireManager.php` | Persistance (JSON + `flock`), unicité du nom. Ne fait aucun `echo`. |
| Contrôleur | `backend/api/groupes.php` | Traduit HTTP ↔ Gestionnaire, choisit les codes HTTP. |
| Frontend | `frontend/pages/admin-groupes-musculaires.html`, `frontend/js/groupes-musculaires.js` | Affichage, validation côté client, états de chargement. |

Diagramme : `docs/diagramme-classes.mmd`. Changement par rapport à la Session 2 : dans `GestionGroupeMusculaire.php`, la classe de gestion faisait aussi les `echo json_encode` et lisait `php://input` ; ici ces rôles sont séparés.

## Lancer

Prérequis : PHP ≥ 8.1 avec l'extension `mbstring`.

```bash
php -S localhost:8000          # depuis la racine du projet
# puis ouvrir http://localhost:8000/frontend/pages/admin-groupes-musculaires.html
php tests/run.php              # tests Entité + Gestionnaire
```

Démo des états asynchrones : `admin-groupes-musculaires.html?delay=1500` (latence simulée → spinners visibles) et `admin-groupes-musculaires.html?fail=1` (erreur 500 → écran d'erreur + toast). Ces deux paramètres sont réservés à la démo.

## API

| Méthode | URL | Succès | Erreurs |
|---|---|---|---|
| GET | `/backend/api/groupes.php[?id=1]` | 200 | 404 |
| POST | `/backend/api/groupes.php` | 201 | 400, 409, 422 |
| PUT | `/backend/api/groupes.php?id=1` | 200 | 400, 404, 409, 422 |
| DELETE | `/backend/api/groupes.php?id=1` | 200 | 400, 404 |

Réponse : `{ "success": bool, "data": …, "message": string|null }`.

## Suivi par Issues GitHub

`scripts/create-issues.sh` crée les labels (`statut:en-cours`, `statut:bloquée`, `statut:terminée`, `type:*`), le milestone et 9 Issues, puis clôture celles déjà livrées. Ensuite, référencer l'Issue dans chaque commit (`feat: entité GroupeMusculaire (#2)`, `Closes #4`) et faire évoluer les labels au fil du travail.
