# CHANGELOG CHIFFRAGE FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

# Change Log
All notable changes to this project will be documented in this file.

## Unreleased



## version 1.8

- FIX : Erreur 500 création tâche depuis chiffrage - *30/08/2023* - 1.8.7
- FIX : Erreur php card à cause du champs module (lié à webhost) - *30/08/2023* - 1.8.6
- FIX : Redirection sur card après création grâce au + sur une liste de chiffrage - *30/08/2023* - 1.8.5
- FIX : Pré-remplissage du champs tiers suite à une création depuis la fiche tiers - *30/08/2023* - 1.8.4
- FIX : Ordonner la liste par dernière date de modification par défaut - *30/08/2023* - 1.8.3
- FIX : Après clône, message erreur qté ne peuvent être inférieures à zero alors que défini sur vide - *30/08/2023* - 1.8.2
- NEW : Ajout des status "Converti" et "Réalisé" dans le filtre de la liste et le menu - *04/08/2023* - 1.8.0
  
## version 1.7

- FIX : déclaration des fields - *05/06/2023* - 1.7.6
- FIX : ajout du test sur array avant count  - *27/06/2023* - 1.7.5   
- FIX : changement de syntaxe dans les filtres de selectForForms - *03/05/2023* - 1.7.4
- FIX : change syntaxe  - *27/04/2023* - 1.7.3  
- FIX : Erreur dans la liste si plusieurs extrafields *31/03/2023* - 1.7.2
- FIX : Ajout d'un extrafield fk_chiffrage sur commandedet (effectuera le lien automatique du chiffrage vers la tâche  ) - *16/02/2023* - 1.7.1  
- NEW : Ajout d'un onglet chiffrage sur la page des tiers *23/08/2022* 1.7.0
- NEW : Changement de statut converti sur création de propal ou de tâche *17/10/2022* 1.7.0
  - changement de statut réalisé si la tâche est à 100 %
  - retour au statut chiffré si supprésion propal ou tâche liée

## Version 1.6

- NEW : Ajout du champ "détail spécification fonctionnelle" *10/05/2022* 1.6.0

## Version 1.5

- NEW : Ajout de la class TechATM pour l'affichage de la page "A propos" *10/05/2022* 1.5.0
- NEW : Ajout de la page extrafield et la possibilité d'en créer *09/05/2022* 1.4.0

## Version 1.3

- NEW : Ajout de la création de tâches via le MassAction & depuis un Chiffrage *08/04/2022* 1.3.0

## Version 1.2

- FIX : Objets liées ticket, chiffrage & redirections *15/04/2022* 1.2.2  
- FIX : Modification de la redirection du bouton Annuler lors de la création d'un chiffrage depuis un ticket *30/03/2022* 1.2.1
- NEW : Ajout dans les objets liés de Qty en JH d'un chiffrage *11/03/2022* - 1.2.0
- NEW : Ajout des liens dans Objets liés lors de la création d'un chiffrage depuis un ticket *11/03/2022* - 1.1.0
- FIX : Modification du filtre 'po_estimate' lors de la création d'un chiffrage *11/03/2022* - 1.0.12

## Version 1.0

- FIX : Ajout d'une condition pour éviter la création d'une propal depuis l'url si le bouton n'est pas présent *04/03/2022* - 1.0.11
- FIX : Fiche chiffage : "Produit / Service" au lieu de "Produit" *03/03/2022* - 1.0.10
- FIX : Lien chiffrage / propale visible sur chiffrage après ajout avec le bouton "Créer Devis" depuis le chiffrage. *03/03/2022* - 1.0.9
- FIX : Nouveau chiffrage : le service par défaut est renseigné *03/03/2022* - 1.0.8
- FIX : Activer par défaut le modèle de numérotation standard *03/03/2022* - 1.0.7
- FIX : Retirer la phrase "Chiffrage setup page" dans la conf *03/03/2022* - 1.0.6
- FIX : Contour de l'onglet paramètre dans la conf *03/03/2022* - 1.0.5
- FIX : Traduction de la conf CHIFFRAGE_DEFAULT_PRODUCT *03/03/2022* - 1.0.4
- FIX : Lien chiffrage / propale visible sur chiffrage après ajout avec l'action en masse *03/03/2022* - 1.0.3
- FIX : Mots-clés : retirer la fonction MAJUSCULES *03/03/2022* - 1.0.2
- FIX : Ajout des lignes sur la propal créé grâce au bouton "Créer Devis" depuis un chiffrage *15/02/2022* - 1.0.1

