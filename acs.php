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
$sNameId = MetaModel::GetModuleSetting('combodo-saml', 'nameid', 'uid');
if ($sNameId == '')
{
	// Enforce a default / non-empty value in case the conf gets garbled
	$sNameId = 'uid';
}

if (strcasecmp($sNameId, 'nameid') == 0)
{
	$sLogin = $oAuth->getNameId();
}
else
{
	if (!array_key_exists($sNameId, $aUserAttributes))
	{
		echo "<p>".Dict::Format('SAML:Error:Invalid_Attribute', $sNameId)."</p>";
		IssueLog::Error("SAML authentication failed because the expected attribute '$sNameId' was not found in the IdP response.");
		IssueLog::Info('Adjust the parameter "nameid" of the module "combodo-saml" in the iTop configuration file to specify a valid attribute or specify "NameID" to use the "subject" of the SAML response as the login.');
		IssueLog::Info('IdP reponse subject contains '.$oAuth->getNameId());
		IssueLog::Info('Available attributes in the IdP response: '.print_r($aUserAttributes, true));
		unset($_SESSION['login_mode']);
		unset($_SESSION['login_will_redirect']);
		exit;
	}
	$sLogin = $aUserAttributes[$sNameId][0];
}
$_SESSION['auth_user'] = $sLogin;
$_SESSION['login_mode'] = 'saml';
unset($_SESSION['login_will_redirect']);

if (isset($_POST['RelayState']) && OneLogin\Saml2\Utils::getSelfURL() != $_POST['RelayState'])
{
	$oAuth->redirectTo($_POST['RelayState'], array('login_saml' => 'connected'));
}
