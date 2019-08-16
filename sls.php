<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */
/**
 *  SP Single Logout Service Endpoint
 */

require_once('../../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

$oConfig = new Combodo\iTop\Extension\Saml\Config();
$oAuth = new OneLogin\Saml2\Auth($oConfig->GetSettings());

unset($_SESSION['auth_user']);
unset($_SESSION['login_mode']);

$oAuth->processSLO();

$aErrors = $oAuth->getErrors();

if (empty($aErrors))
{
	$oPage = LoginWebPage::NewLoginWebPage();
	$oPage->DisplayLogoutPage(false);
	exit;
}
else
{
	echo '<p>'.Dict::S('SAML:Error:ErrorOccurred').'</p>';
	echo implode(', ', $aErrors);
}
