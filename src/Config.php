<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

namespace Combodo\iTop\Extension\Saml;

require_once(APPROOT.'/application/utils.inc.php');
require_once(APPROOT.'/core/metamodel.class.php');

use MetaModel;
use utils;


class Config
{
	public function GetSettings()
	{
		$aSP = MetaModel::GetModuleSetting('combodo-saml', 'sp', array());
		$aIDP = MetaModel::GetModuleSetting('combodo-saml', 'idp', array());
		$sEntityId = utils::GetAbsoluteUrlModulesRoot().'combodo-saml';

		$aSettings = array (
			// If 'strict' is True, then the PHP Toolkit will reject unsigned
			// or unencrypted messages if it expects them to be signed or encrypted.
			// Also it will reject the messages if the SAML standard is not strictly
			// followed: Destination, NameId, Conditions ... are validated too.
			'strict' => MetaModel::GetModuleSetting('combodo-saml', 'strict', true),
			
			// Enable debug mode (to print errors).
			'debug' => MetaModel::GetModuleSetting('combodo-saml', 'debug', true),
			
			// Set a BaseURL to be used instead of try to guess
			// the BaseURL of the view that process the SAML Message.
			// Ex http://sp.example.com/
			//    http://example.com/sp/
			'baseurl' => MetaModel::GetModuleSetting('combodo-saml', 'baseurl', $sEntityId),
			
			// Service Provider Data that we are deploying.
			'sp' => $aSP,
			
			// Identity Provider Data that we want connected with our SP.
			'idp' => $aIDP,
		);

		if (!isset($aSettings['sp']['entityId']))
		{
			$aSettings['sp']['entityId'] = $sEntityId;
		}
		if (!isset($aSettings['sp']['assertionConsumerService']['url']))
		{
			$sACSUrl = utils::GetAbsoluteUrlModulesRoot().'combodo-saml/acs.php';
			$aSettings['sp']['assertionConsumerService']['url'] = $sACSUrl;
		}
		if (!isset($aSettings['sp']['singleLogoutService']['url']))
		{
			$sSLSUrl = utils::GetAbsoluteUrlModulesRoot().'combodo-saml/sls.php';
			$aSettings['sp']['singleLogoutService']['url'] = $sSLSUrl;
		}

		return $aSettings;
	}
}