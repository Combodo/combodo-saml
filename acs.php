<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

/**
 *  SP Assertion Consumer Service Endpoint
 */
require_once('../../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

$oConfig = new Combodo\iTop\Extension\Saml\Config();
$oAuth = new OneLogin\Saml2\Auth($oConfig->GetSettings());
$oAuth->processResponse();

$aErrors = $oAuth->getErrors();

if (!empty($aErrors))
{
	echo '<p><b>'.Dict::S('SAML:Error:ErrorOccurred').'</b></p>';
	echo '<p>', implode(', ', $aErrors), '</p>';
	exit();
}

if (!$oAuth->isAuthenticated()) {
	echo "<p>".Dict::S('SAML:Error:NotAuthenticated')."</p>";
	exit();
}

$aUserAttributes = $oAuth->getAttributes();
$_SESSION['auth_user'] = $aUserAttributes['uid'][0];
$_SESSION['login_mode'] = 'saml';
unset($_SESSION['login_will_redirect']);

if (isset($_POST['RelayState']) && OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState'])
{
	$oAuth->redirectTo($_POST['RelayState'], array('login_saml' => 'connected'));
}
