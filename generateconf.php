<?php

require_once('../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

/////////////////////////////////////////////////////////////////////
// Main program
//
LoginWebPage::DoLogin(); // Check user rights and prompt if needed
ApplicationMenu::CheckMenuIdEnabled('ConfigGenerateSimpleSaml');

$sPath = utils::GetAbsoluteUrlModulesRoot().'combodo-saml';
$aSP = MetaModel::GetModuleSetting('combodo-saml', 'sp', array());
$sACSBinding = $aSP['assertionConsumerService']['binding'];
$sSLSBinding = $aSP['singleLogoutService']['binding'];

$sConfig = '$metadata[\''.$sPath.'\'] = array (
	\'SingleLogoutService\' =>
		array (
			0 =>
				array (
					\'Binding\' => \''.$sSLSBinding.'\',
					\'Location\' => \''.$sPath.'/sls.php\',
				),
		),
	\'AssertionConsumerService\' =>
		array (
			0 =>
				array (
					\'index\' => 0,
					\'Binding\' => \''.$sACSBinding.'\',
					\'Location\' => \''.$sPath.'/acs.php\',
				),
			1 =>
				array (
					\'index\' => 1,
					\'Binding\' => \'urn:oasis:names:tc:SAML:1.0:profiles:browser-post\',
					\'Location\' => \''.$sPath.'/acs.php\',
				),
			2 =>
				array (
					\'index\' => 2,
					\'Binding\' => \'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact\',
					\'Location\' => \''.$sPath.'/acs.php\',
				),
			3 =>
				array (
					\'index\' => 3,
					\'Binding\' => \'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01\',
					\'Location\' => \''.$sPath.'/acs.php\',
				),
		),
);
';

$sTitle = Dict::S('SAML:SimpleSaml:GenerateSimpleSamlConf');
$oP = new iTopWebPage($sTitle);
$oP->add(Dict::S('SAML:SimpleSaml:Instructions'));
$oP->add("<pre>");
$oP->add("\n\n");
$oP->add($sConfig);
$oP->add("\n");
$oP->add("</pre>");
$oP->output();

