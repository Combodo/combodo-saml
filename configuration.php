<?php
/**
 * @copyright   Copyright (C) 2019-2020 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */
require_once('../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');

use Combodo\iTop\Extension\Saml\Config;

function DisplayInputForm(WebPage $oP, $sUrl, $sRawXml)
{
	$sSafeUrl = htmlentities($sUrl, ENT_QUOTES, 'UTF-8');
	$oP->add(
<<<HTML
	<h2>Importing the Identity Provider meta data</h2>
	<form method="post">
	<p>Enter the URL of the meta data from the Identity Provider (IdP):</p>
	<p><input type="text" size="50" name="url" placeholder="https://my-idp-server/metadata" value="$sSafeUrl"></input></p>
	HTML
	);
	$oP->StartCollapsibleSection('Paste the XML meta data:', false, 'xml_direct_input');
	$sSafeXml = htmlentities($sRawXml, ENT_QUOTES, 'UTF-8');
	$oP->add(
<<<HTML
	    <p><textarea name="xml_meta_data" style="width: 30rem; height:10rem;">$sSafeXml</textarea></p>
	HTML
	    );
	$oP->EndCollapsibleSection();
	$oP->add(
<<<HTML
		<p><button type="submit">Check Meta Data</button></p>
		<input type="hidden" name="operation" value="check"/>
	</form>
	HTML
	    );
	
}

function CheckMetaData(WebPage $oP, $sUrl, $sRawXml)
{
	DisplayInputForm($oP, $sUrl, $sRawXml);
	$aErrors = array();
	$sMetaData = GetMetaData($sUrl, $sRawXml);
	if ($sMetaData === false)
	{
		$aErrors[] = 'Failed to read the XML data from the supplied URL';
		$aIdP = array();
	}
	else if (($sUrl == '') && ($sRawXml == ''))
	{
	    $aErrors[] = 'Please either supply a valid URL or paste the XML meta data';
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
		$sSafeXml = htmlentities($sRawXml, ENT_QUOTES, 'UTF-8');
		$oP->add(
<<<HTML
		<div class="header_message message_ok">Ok, the meta data look correct.</div>
		<form method="post">
		<input type="hidden" name="operation" value="update"/>
		<input type="hidden" name="url" value="$sSafeURL"/>
		<input type="hidden" name="xml_meta_data" value="$sSafeXml"/>
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

function UpdateIdPConfiguration(WebPage $oP, $sUrl, $sRawXml)
{
	$sMetaData = GetMetaData($sUrl, $sRawXml);

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

/**
 * Get the meta data from the URL or directly from the form when
 * no URL is supplied.
 *
 * @param string $sUrl
 * @param string $sXmlMetaData
 * @return string|false Returns false when the URL cannot be read
 */
function GetMetaData($sUrl, $sXmlMetaData)
{
    if (empty($sUrl))
    {
        return $sXmlMetaData;
    }
    else
    {
        return @file_get_contents($sUrl);
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

	DisplayInputForm($oP, '', '');
	
	$aSettings = Config::GetSettings();
	$sSafePrivateKey = isset($aSettings['sp']['private_key']) ? htmlentities($aSettings['sp']['private_key'], ENT_QUOTES, 'UTF-8') : '';
	$sSafeX509Cert = isset($aSettings['sp']['x509cert']) ? htmlentities($aSettings['sp']['x509cert'], ENT_QUOTES, 'UTF-8') : '';
	$oP->add(
<<<HTML
	<hr/>
	<form id="certificate_form" method="post">
	<input type="hidden" name="operation" value="update_certificate"/>
	<h2>Configuring the iTop Service Provider private key and certificate</h2>
	<p>Enter the X509 certificate and the private key to use for signing iTop's SAML requests.</p>
	<p>If you skip this configuration the requests from iTop to the Identity Provider will NOT be signed.</p>
	<p>Private Key:</p>
	<p><textarea style="width:30rem;height:10rem;" name="private_key" placeholder="-----BEGIN RSA PRIVATE KEY-----
	...
	...
	...
	-----END RSA PRIVATE KEY-----
	">$sSafePrivateKey</textarea></p>
	<p>X509 certificate:</p>
	<p><textarea style="width:30rem;height:10rem;" name="x509cert" placeholder="-----BEGIN CERTIFICATE-----
	...
	...
	...
	-----END CERTIFICATE-----">$sSafeX509Cert</textarea></p>
	<p><button type="submit">Save keys</button></p>
	</form>
HTML
	    );
	
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

/**
 * Update iTop configuration to set/unset the certificate and the security flags
 *
 * @param WebPage $oP
 */
function UpdateCertificate(WebPage $oP)
{
	$oConf = Metamodel::GetConfig();
	$sX509Cert = utils::ReadPostedParam('x509cert', '', false, 'raw_data');
	$sPrivateKey = utils::ReadPostedParam('private_key', '', false, 'raw_data');

	$aSP = $oConf->GetModuleSetting('combodo-saml', 'sp', array());
	$aSP['entityId'] = utils::GetAbsoluteUrlModulesRoot() . 'combodo-saml';
	$aSP['x509cert'] = $sX509Cert;
	$aSP['private_key'] = $sPrivateKey;
	$oConf->SetModuleSetting('combodo-saml', 'sp', $aSP);

	$aSecurity = $oConf->GetModuleSetting('combodo-saml', 'security', array());
	if ($sX509Cert != '')
	{
		// When a certificate is configured, request that the messages be signed
		$aSecurity['wantMessagesSigned'] = true;
		$aSecurity['wantAssertionsSigned'] = true;
		$aSecurity['authnRequestsSigned'] = true;
		$aSecurity['logoutRequestSigned'] = true;
		$aSecurity['logoutResponseSigned'] = true;
	}
	else
	{
		// No certificate, don't try to sign the messages !
		$aSecurity['wantMessagesSigned'] = false;
		$aSecurity['wantAssertionsSigned'] = false;
		$aSecurity['authnRequestsSigned'] = false;
		$aSecurity['logoutRequestSigned'] = false;
		$aSecurity['logoutResponseSigned'] = false;
	}
	$oConf->SetModuleSetting('combodo-saml', 'security', $aSecurity);

	@chmod($oConf->GetLoadedFile(), 0770); // Allow overwriting the file
	$oConf->WriteToFile();
	@chmod($oConf->GetLoadedFile(), 0444); // Read-only

	$oP->add('<div class="header_message message_ok">iTop (Service Provider) Configuration updated!!</div>');
	DisplayWelcomePage($oP);
}

function DisplaySPMetaDataAsPHP(WebPage $oP)
{
	$aSP = Config::GetSPSettings();

	$sEntityId = $aSP['entityid'];

	$sSimpleSamlConf = '$aMetadata["' . $sEntityId . '"] = ' . var_export($aSP, true);

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
	$sRawXml = utils::ReadParam('xml_meta_data', '', false, 'raw_data');

	switch($sOperation)
	{
		case 'update':
			UpdateIdPConfiguration($oP, $sUrl, $sRawXml);
			break;

		case 'check':
			CheckMetaData($oP, $sUrl, $sRawXml);
			break;

		case 'idp':
			DisplayInputForm($oP, $sUrl, $sRawXml);
			break;
			
		case 'update_certificate':
			UpdateCertificate($oP);
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
