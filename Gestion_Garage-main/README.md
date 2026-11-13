
# Mécano D-CLIC

Application Laravel de gestion d’un garage automobile : clients, véhicules, réparations, pièces, facturation et annonces de véhicules d’occasion.

## Installation locale

1. Configurez la base de données et les paramètres d’application dans `.env`.
2. Installez les dépendances avec `composer install` et `npm install`.
3. Exécutez `php artisan migrate`.
4. Exécutez `php artisan db:seed` pour les données d’exemple, dont les dix annonces d’occasion.
5. Compilez les ressources avec `npm run build`, puis lancez `php artisan serve`.

Les espaces administrateur, technicien et client exigent une session ouverte et sont limités au rôle correspondant. L’inscription publique crée uniquement un compte client. Les connexions sont limitées en fréquence.

Les comptes de démonstration sont `GUETABA JACQUES` (`adminguetaba@gmail.com`, administrateur), `JEAN LUCHA` (`technicien08@gmail.com`, technicien), `Samir Benali` (`technicien08+2@gmail.com`, second technicien), `THEUG LUCHA` (`client02@gmail.com`, client) et `YAO SIVI` (`client02+2@gmail.com`, second client). Les suffixes `+2` gardent les comptes distincts tout en permettant leur distribution vers les adresses Gmail correspondantes. Ils sont localisés à Lomé, Togo, avec des numéros de démonstration au format togolais `+228`. Les profils sont affichés sans photo. Le mot de passe commun est `J0ean@0809` ; remplacez-le avant toute utilisation réelle.

## Notifications par e-mail

Lorsqu’une intervention est marquée comme prête, le technicien peut envoyer au client l’e-mail de restitution depuis sa fiche. Configurez un transport SMTP réel dans `.env` (`MAIL_MAILER=smtp`, hôte, port, identifiants, chiffrement et expéditeur) avant l’envoi. Le transport `log` sert uniquement au développement et ne remet pas de message au client ; l’application affiche alors un avertissement et permet de réessayer après configuration.

## Annonces de véhicules d’occasion

Le catalogue est accessible après connexion depuis les espaces des utilisateurs. Il présente les annonces disponibles, avec recherche, filtre de budget en FCFA, caractéristiques, prix en FCFA et photographies. Seul l’administrateur peut saisir un nouveau prix inférieur au prix actuel ; les autres utilisateurs peuvent contacter le garage. Les annonces initiales sont mises à jour de manière idempotente par `VehiculeOccasionSeeder`.

## Crédit photo

La photo du Mercedes-AMG GLE 63 S utilisée dans le logo provient de [Wikimedia Commons](https://commons.wikimedia.org/wiki/File:2023_Mercedes-AMG_GLE_63_S_4MATIC_DSC_8921.jpg). Photo : Alexander Migl, sous licence [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0/).

La photo nette d’atelier utilisée en arrière-plan provient de [Wikimedia Commons](https://commons.wikimedia.org/wiki/File:Car_mechanic_worker_repairing_suspension_of_lifted_automobile_at_auto_repair_garage_shop.jpg). Photo : Shixart1985, sous licence [CC BY 2.0](https://creativecommons.org/licenses/by/2.0/).
