<?php
/**
 * Copyright (C) 2019-2020 Combodo SARL
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 */

/**
 * UNAUTHENTICATED page to export the SP Meta Data of iTop for configuring your SAML IdP
 */
require_once('../approot.inc.php');
require_once (APPROOT.'bootstrap.inc.php');
require_once (APPROOT.'application/startup.inc.php');
require_once (APPROOT.'application/ajaxwebpage.class.inc.php');

use Combodo\iTop\Extension\Saml\Config;

$aSP = Config::GetSPSettings();

$doc  = new DOMDocument('1.0', 'utf-8');
$doc->formatOutput = true;
$root = $doc->createElementNS(Config::META_DATA_NS, 'md:EntityDescriptor');
$root->setAttribute('entityID', $aSP['entityid']);
$doc->appendChild($root);

$oSPSSODesc = $doc->createElementNS(Config::META_DATA_NS, 'md:SPSSODescriptor');
$oSPSSODesc->setAttribute('protocolSupportEnumeration', 'urn:oasis:names:tc:SAML:2.0:protocol');
$root->appendChild($oSPSSODesc);

$oSLO = $doc->createElementNS(Config::META_DATA_NS, 'md:SingleLogoutService');
$oSLO->setAttribute('Binding', $aSP['SingleLogoutService']['Binding']);
$oSLO->setAttribute('Location', $aSP['SingleLogoutService']['Location']);
$oSPSSODesc->appendChild($oSLO);

$oSSO = $doc->createElementNS(Config::META_DATA_NS, 'md:AssertionConsumerService');
$oSSO->setAttribute('index', '1');
$oSSO->setAttribute('Binding', $aSP['AssertionConsumerService']['Binding']);
$oSSO->setAttribute('Location', $aSP['AssertionConsumerService']['Location']);
$oSPSSODesc->appendChild($oSSO);

if(isset($aSP['key']) && ($aSP['key'] != ''))
{
    $oKeyDescriptor = $doc->createElementNS(Config::META_DATA_NS, 'md:KeyDescriptor');
    $oKeyDescriptor->setAttribute('use', 'signing');
    $oSPSSODesc->appendChild($oKeyDescriptor);
    
    $oKeyInfo = $doc->createElementNS(Config::DIGITAL_SIGNATURE_NS, 'ds:KeyInfo');
    $oKeyDescriptor->appendChild($oKeyInfo);
    
    $oX509Data = $doc->createElementNS(Config::DIGITAL_SIGNATURE_NS, 'ds:X509Data');
    $oKeyInfo->appendChild($oX509Data);
    
    $oX509Cert = $doc->createElementNS(Config::DIGITAL_SIGNATURE_NS, 'ds:X509Certificate', $aSP['key']);
    $oX509Data->appendChild($oX509Cert);
    
    //TODO ?? If we request encryption, then the KeyDescriptor node must be duplicated with the attribute use="encryption"
}

$oP = new ajax_page('');
$oP->SetContentType('application/xml;charset=UTF-8');
$oP->add($doc->saveXML());
$oP->output();
