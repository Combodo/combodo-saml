<?php

require_once('../../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

$sPath = utils::GetAbsoluteUrlModulesRoot().'combodo-saml';

$sConfig = '$metadata[\''.$sPath.'\'] = array (
	\'SingleLogoutService\' =>
		array (
			0 =>
				array (
					\'Binding\' => \'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect\',
					\'Location\' => \''.$sPath.'/sls.php\',
				),
		),
	\'AssertionConsumerService\' =>
		array (
			0 =>
				array (
					\'index\' => 0,
					\'Binding\' => \'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST\',
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

echo "<pre>\n";
echo "Append this conf to : simplesamlphp/metadata/saml20-sp-remote.php\n\n";
echo $sConfig;