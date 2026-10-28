#!/usr/bin/env bash
# Crée les labels, le milestone et les Issues du projet, puis clôture celles déjà livrées.
# Prérequis : GitHub CLI (https://cli.github.com) + `gh auth login`.
# Usage : cd <dépôt cloné> && bash scripts/create-issues.sh
set -euo pipefail

MILESTONE="Projet technique - Session 3"
gh api -X POST "repos/{owner}/{repo}/milestones" -f title="$MILESTONE" >/dev/null 2>&1 || true

gh label create "type:feature"     --color 1d76db --force >/dev/null
gh label create "type:docs"        --color 0075ca --force >/dev/null
gh label create "type:test"        --color a2eeef --force >/dev/null
gh label create "statut:en-cours"  --color fbca04 --force >/dev/null
gh label create "statut:bloquée"   --color d93f0b --force >/dev/null
gh label create "statut:terminée"  --color 0e8a16 --force >/dev/null

# issue "titre" "labels" "corps" "done|open"
issue() {
  local url n
  url=$(gh issue create --title "$1" --label "$2" --milestone "$MILESTONE" --body "$3")
  n=${url##*/}
  echo "#$n  $1"
  if [[ "$4" == "done" ]]; then
    gh issue close "$n" --reason completed \
      --comment "Livré. Voir les commits qui référencent #$n sur la branche Session-3." >/dev/null
  fi
}

issue "Conception : diagramme de classes Entité / Gestionnaire" "type:docs,statut:terminée" \
"Produire docs/diagramme-classes.mmd.
- [x] Entité, Gestionnaire, Contrôleur et leurs dépendances" done

issue "Entité GroupeMusculaire avec validation métier" "type:feature,statut:terminée" \
"- [x] Nom et description obligatoires, longueurs max
- [x] toArray / fromArray, aucune dépendance au stockage ni à HTTP" done

issue "Gestionnaire GroupeMusculaireManager (CRUD + verrou de fichier)" "type:feature,statut:terminée" \
"- [x] findAll / find / create / update / delete
- [x] Unicité du nom (insensible à la casse)
- [x] flock pour éviter les écritures concurrentes" done

issue "API REST : contrôleur mince et codes HTTP" "type:feature,statut:terminée" \
"- [x] GET / POST / PUT / DELETE
- [x] 200, 201, 400, 404, 405, 409, 422, 500 avec JSON {success, data, message}" done

issue "Interface Tailwind mobile-first (design Session 2 conservé)" "type:feature,statut:terminée" \
"- [x] Sidebar empilée sur mobile (md:flex-row), tableau défilable horizontalement
- [x] Design d origine de la Session 2 inchangé" done

issue "Feedbacks asynchrones : spinners, toasts, états chargement/erreur/vide" "type:feature,statut:terminée" \
"- [x] Spinner de chargement initial, sur Enregistrer et sur Supprimer
- [x] Toasts succès / erreur
- [x] Écran d'erreur avec bouton Réessayer
- [x] Messages d'erreur par champ" done


issue "Tests : entité + gestionnaire (php tests/run.php)" "type:test,statut:terminée" \
"- [x] 11 vérifications automatisées" done

issue "Migration du stockage JSON vers MySQL/PDO (table muscle_groups de la Session 1)" "type:feature,statut:bloquée" \
"Remplacer le JSON par PDO en ne modifiant que le Gestionnaire.
**Bloquée** : la Session 1 utilise une table MySQL \`muscle_groups\`, la Session 2 un fichier JSON ; il faut d'abord valider le schéma SQL commun (colonnes, unicité du nom).
- [ ] Valider le schéma
- [ ] Écrire GroupeMusculaireManagerPdo
- [ ] Rejouer tests/run.php" open

issue "Authentification admin sur l'API" "type:feature,statut:en-cours" \
"- [ ] Protéger POST / PUT / DELETE (session ou token)" open
