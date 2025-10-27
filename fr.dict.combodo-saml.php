<?php
/**
 * Localized data
 *
 * @copyright Copyright (C) 2010-2024 Combodo SAS
 * @license    https://opensource.org/licenses/AGPL-3.0
 * 
 */
/**
 *
 */
Dict::Add('FR FR', 'French', 'Français', [
	'Menu:SAMLConfiguration' => 'Configuration SAML',
	'SAML:Error:CheckTheLogFileForMoreInformation' => 'Voir le fichier journal pour plus information',
	'SAML:Error:ErrorOccurred' => 'Une erreur est survenue',
	'SAML:Error:Invalid_Attribute' => 'L\'authentification SAML a échoué car l\'attribut attendu \'%1$s\' n\'est pas présent dans le réponse de l\'Identity Provider (IdP). Consultez le fichier error.log pour plus d\'informations.',
	'SAML:Error:NotAuthenticated' => 'Non authentifié',
	'SAML:Error:UserNotAllowed' => 'Utilisateur non autorisé',
	'SAML:Login:SignIn' => 'S\'identifier avec SAML',
	'SAML:Login:SignInTooltip' => 'Cliquer ici pour s\'identifier avec le serveur SAML',
	'SAML:SimpleSaml:GenerateSimpleSamlConf' => 'Générer la configuration SimpleSaml',
	'SAML:SimpleSaml:Instructions' => 'Ajouter ces lignes à la configuration: simplesamlphp/metadata/saml20-sp-remote.php',
	'Menu:DelegatedAuthentication' => 'Authentification déléguée',
	'Menu:DelegatedAuthentication+'=> 'Configurer l\'authentification via un fournisseur de service',
]);
