<?php
namespace Combodo\iTop\Test\UnitTest\Core\CombodoSaml;

use Combodo\iTop\Extension\Saml\Config;

class IdPParsingTest extends ItopTestCase
{
	/**
	 * Test that we are able to parse the meta information returned by a SimpleSAML Identity Provider
	 */
	public function TestParseIdPSimpleSaml()
	{
		$sXML = file_get_contents(__DIR__.'/asset/simple-saml.xml');
		$aErrors = array();
		$aIdP = Config::ParseIdPMetaData($sXML, $aErrors);
		$this->assertEmpty($aErrors);
		$this->assertEquals($aIdP['entityId'], 'https://idp.test.com/simplesaml/saml2/idp/metadata.php');
		$this->assertEquals(count($aIdP['x509certMulti']), 2);
		$this->assertEquals(count($aIdP['singleSignOnService']), 1);
		$this->assertEquals(count($aIdP['singleLogoutService']), 1);
	}
	
	/**
	 * Test that we are able to parse the meta information returned by a Keycloak Identity Provider
	 */
	public function TestParseIdPKeycloak()
	{
		$sXML = file_get_contents(__DIR__.'/asset/keycloak.xml');
		$aErrors = array();
		$aIdP = Config::ParseIdPMetaData($sXML, $aErrors);
		$this->assertEmpty($aErrors);
		$this->assertEquals($aIdP['entityId'], 'https://idp.test.com/auth/realms/idp');
		$this->assertEquals(count($aIdP['x509cert']), 1);
		$this->assertEquals(count($aIdP['singleSignOnService']), 1);
		$this->assertEquals(count($aIdP['singleLogoutService']), 1);
	}
}

