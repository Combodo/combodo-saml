<?php

/**
 * @copyright   Copyright (C) 2010-2026 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */
/**
 * Localized data
 */

Dict::Add('DE DE', 'German', 'Deutsch', [
	'SAML:Error:UserNotAllowed' => 'Benutzer nicht zugelassen',
	'SAML:Error:ErrorOccurred' => 'Es ist ein Fehler aufgetreten',
	'SAML:Error:CheckTheLogFileForMoreInformation' => 'Weitere Informationen finden Sie in der Protokolldatei "log/saml.log".',
	'SAML:Error:NotAuthenticated' => 'Nicht authentifiziert',
	'SAML:SimpleSaml:GenerateSimpleSamlConf' => 'Konfiguration für SimpleSaml erzeugen',
	'SAML:SimpleSaml:Instructions' => 'Fügen Sie diese Konfiguration an folgende Datei an: simplesamlphp/metadata/saml20-sp-remote.php',
	'SAML:Login:SignIn' => 'Mit SAML anmelden',
	'SAML:Login:SignInTooltip' => 'Klicken Sie hier, um sich am SAML-Server zu authentifizieren',
	'Menu:SAMLConfiguration' => 'SAML-Konfiguration',
	'SAML:Error:Invalid_Attribute' => 'Die SAML-Authentifizierung ist fehlgeschlagen, da das erwartete Attribut \'%1$s\' nicht in der Antwort des Identity Providers (IdP) enthalten war. Weitere Informationen finden Sie in der Datei error.log.',
	'Menu:DelegatedAuthentication' => 'Delegierte Authentifizierung',
	'Menu:DelegatedAuthentication+' => 'Delegation der Authentifizierung an einen externen Anbieter konfigurieren',
]);
