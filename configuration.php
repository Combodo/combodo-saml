<?php

require_once('../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

use Combodo\iTop\Extension\Saml\Config;

function DisplayInputForm(WebPage $oP, $sUrl)
{
	$sSafeUrl = htmlentities($sUrl, ENT_QUOTES, 'UTF-8');
	$oP->add(
<<<HTML
<h2>Importing the Identity Provider meta data</h2>
<form method="post">
	<p>Enter the URL of the meta data from the Identity Provider (IdP):</p>
	<p><input type="text" size="50" name="url" placeholder="https://my-idp-server/metadata" value="$sSafeUrl"></input></p>
	<p><button type="submit">Check Meta Data</button></p>
	<input type="hidden" name="operation" value="check"/>
</form>
HTML
	);
}

function CheckMetaData(WebPage $oP, $sUrl)
{
	DisplayInputForm($oP, $sUrl);
	$aErrors = array();
	$sMetaData = @file_get_contents($sUrl);
	if ($sMetaData === false)
	{
		$aErrors[] = 'Failed to read the XML data from the supplied URL';
		$aIdP = array();
	}
	else
	{
		$aIdP = Config::ParseIdPMetaData($sMetaData, $aErrors);
	}
	
	if (count($aErrors) > 0)
	{
		$oP->add('<div class="header_message message_error">');
		foreach($aErrors as $sError)
		{
			$oP->p(htmlentities($sError, ENT_QUOTES, 'UTF-8'));
		}
		$oP->add('</div>');
	}
	else
	{
		$sSafeURL = htmlentities($sUrl, ENT_QUOTES, 'UTF-8');
		$oP->add(
<<<HTML
<div class="header_message message_ok">Ok, the meta data look correct.</div>
<form method="post">
<input type="hidden" name="operation" value="update"/>
<input type="hidden" name="url" value="$sSafeURL"/>
<button type="submit">Update iTop Configuration</button>
</form>
HTML
		);
		$oP->StartCollapsibleSection('PHP configuration:', false, 'saml_conf');
		$oP->add('<pre>'.var_export($aIdP, true).'</pre>');
		$oP->EndCollapsibleSection();
	}
	$oP->StartCollapsibleSection('Raw Meta Data:', false, 'saml_metadata');
	$oP->add('<pre>'.htmlentities($sMetaData, ENT_QUOTES, 'UTF-8').'</pre>');
	$oP->EndCollapsibleSection();
}

function UpdateIdPConfiguration(WebPage $oP, $sUrl)
{
	$sMetaData = file_get_contents($sUrl);

	$aErrors = array();
	$aIdP = Config::ParseIdPMetaData($sMetaData, $aErrors);
	if (count($aErrors) == 0)
	{
		$oConf = Metamodel::GetConfig();

		// Make sure that SAML is enabled
		$aAllowedLoginTypes = $oConf->GetAllowedLoginTypes();
		if (!in_array('saml', $aAllowedLoginTypes))
		{
			// Add 'saml' after 'form'
			$aModifiedLoginTypes = array();
			foreach($aAllowedLoginTypes as $sType)
			{
				$aModifiedLoginTypes[] = $sType;
				if ($sType == 'form')
				{
					$aModifiedLoginTypes[] = 'saml';
				}
			}
			$oConf->SetAllowedLoginTypes($aModifiedLoginTypes);
		}

		$oConf->SetModuleSetting('combodo-saml', 'idp', $aIdP);
		@chmod($oConf->GetLoadedFile(), 0770); // Allow overwriting the file
		$oConf->WriteToFile();
		@chmod($oConf->GetLoadedFile(), 0444); // Read-only
		$oP->add('<div class="header_message message_ok">iTop Configuration updated!!</div>');
	}
	else 
	{
		$oP->add('<div class="header_message message_error">');
		foreach($aErrors as $sError)
		{
			$oP->p(htmlentities($sError, ENT_QUOTES, 'UTF-8'));
		}
		$oP->add('</div>');
		$oP->add('<div class="header_message message_info">The iTop Configuration <b>was not updated</b>!!</div>');
	}
}

function DisplayWelcomePage(WebPage $oP)
{
	$sModuleURL = utils::GetAbsoluteUrlModulesRoot().'/combodo-saml';
	
	$oP->add(
<<<HTML
<h1>Single Sign-On configuration using SAML</h1>
<p><img src="$sModuleURL/asset/img/SAML-configuration.svg"></p>
<p>To enable the Single Sign On (SSO) based on SAML in iTop, you have to configure both:</p>
<ul>
<li>iTop as a SAML <b>Service Provider</b> (SP) connected to your SAML server</li>
<li>Your SAML server as a SAML <b>Identity Provider</b> (IdP) accepting this instance of iTop</li>
</ul>
<p>This configuration is done by echanging XML meta data between both systems.
You must export the meta data describing iTop as a Service provider to your SAML server in order to allow iTop to use the Identity Provider.
Similarly you must configure the Identity Provider to be used by iTop. This is achieved by importing the XML meta data published by your SAML server into iTop</p>
<hr/>
HTML
	);

	DisplayInputForm($oP, '');

	$sMetaDataURI = utils::GetAbsoluteUrlModulePage('combodo-saml', "sp-metadata.php");
	
	$oP->add(
<<<HTML
<hr/>
<h2>Exporting iTop's Service Provider meta data</h2>
<p>Use the following link to export iTop's meta data: <a target="_blank" href="$sMetaDataURI">Meta Data Export</a></p>
HTML
	);
	$oP->StartCollapsibleSection('PHP Version, specific for SimpleSAML', false, 'php-meta-data');
	DisplaySPMetaDataAsPHP($oP);
	$oP->EndCollapsibleSection();
}


function DisplaySPMetaDataAsPHP(WebPage $oP)
{
	$aSP = Config::GetSPSettings();
	
	$sEntityId = $aSP['entityid'];
	
	$sSimpleSamlConf = '$aMetadata["'.$sEntityId.'"] = '.var_export($aSP, true);
	
	$oP->p(Dict::S('SAML:SimpleSaml:Instructions'));
	$oP->add('<textarea style="width:100%;display:block;height:35em;">');
	$oP->add($sSimpleSamlConf);
	$oP->add("</textarea>");
}

/////////////////////////////////////////////////////////////////////
// Main program
//
LoginWebPage::DoLogin(); // Check user rights and prompt if needed
ApplicationMenu::CheckMenuIdEnabled('SAMLConfiguration');

$oP = new iTopWebPage('SAML Configuration');
try
{
	$sOperation = utils::ReadParam('operation', '');
	$sUrl = utils::ReadParam('url', '', false, 'raw_data');

	switch($sOperation)
	{
		case 'update':
			UpdateIdPConfiguration($oP, $sUrl);
			break;

		case 'check':
			CheckMetaData($oP, $sUrl);
			break;

		case 'idp':
			DisplayInputForm($oP, $sUrl);
			break;
			
		default:
			DisplayWelcomePage($oP);
	}
}
catch (Exception $e)
{
	$oP->p('ERROR: '.$e->getMessage());
}
$oP->output();