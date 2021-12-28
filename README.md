# Twitch - Défi N°2

## Objectifs
* Concevoir un site pour proposer des idées de side projects
* Rester en vie à la fin des 2 heures

## Règles
* Vous démarrez avec 30 points de vie
* Vous avez 2 heures pour concevoir le projet
* Vous utiliserez Symfony 6 et PHP8.0
* Autorisaton d'un boilerplate Symfony + Webpack + SASS
* Interdiction d'utiliser le composant **Maker**
* Interdiction d'ouvrir votre navigateur
* Interdiction d'éxecuter un test
* Interdiction de lancer une analyse du code avec PHPStan
* Interdiction d'utiliser la documentation
* PHPStan réglé au niveau 9
* Autorisation du backseat
* Règle du 5/10/20
* Interdiction d'utiliser AbstractController
* Interdiction de râler
* Maximum 5 requêtes SQL par requête HTTP

## Résultats
* Fonctionnalité non implémentée : -3 points
* Une erreur PHPStan : -1 point
* Un test qui échoue : -2 points
* 2h sans râler : +5 points
* Ne pas utiliser l'AbstractController : +2 points
* 5-10-20 : +5 points
* Voir le documentation : -2 points
* Plus de 3 requêtes SQL/HTTP : -1 point
* Moins de 3 requêtes SQL/HTTP : +1 point
* Si vous dépassez le temps imparti : -2 points par tranche de 10 minutes
* Si responsive (mobile/tablette/desktop) : +3 points
* No bootstrap : +5 points
* Fonctionnalité bonus : +3 points
* Si le défi est réussi et que la prédiction est en phase : -5 points
* Si le défi est réussi et que la prédiction n'est pas en phase : +5 points
* Si le défi n'est pas réussi et que la prédiction est en phase : -10 points
* Si le défi n'est pas réussi et que la prédiction n'est pas en phase : +5 points
* Clash of code au bout d'une heure, si premier : +3 points
* Sinon : -3 points

## Fonctionnalités à concevoir
* Connexion
* Inscription
* Ajout d'une idée
* Modifier une idée
* Supprimer une idée
* Lister, paginer, trier et filtrer les idées
* Liker une idée
* Commenter une idée
* Modifier son avatar

Bonus :
* Supprimer un commentaire
* Pouvoir repondre à un commentaire
* Liker un commentaire

## Règles métier
* Un utilisateur possède
    * Une adresse email
    * Un pseudo
    * Un mot de passe
    * Un avatar

* Une idée possède
    * Un titre
    * Un contenu
    * Une date de publication
    * Des likes
    * Un slug
    * Un auteur
    * Des tags
    * Une liste de commentaires

* Un tag possède
    * Un nom

* Un commentaire
    * Un auteur
    * Rattaché à une idée
    * Une date de publication
    * Un contenu
    * Des likes (bonus)
    * Commentaire parent (bonus)

* Une idée peut être supprimer ou modifier seulement par son auteur
* Un commentaire peut être supprimer seulement par son auteur
