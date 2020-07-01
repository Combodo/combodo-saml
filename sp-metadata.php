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

$oP = new ajax_page('');
$oP->SetContentType('application/xml;charset=UTF-8');
$oP->add($doc->saveXML());
$oP->output();
