<?php
namespace Combodo\iTop\Test\UnitTest\Core\CombodoSaml;

use Combodo\iTop\Extension\Saml\Config;
use Combodo\iTop\Test\UnitTest\ItopTestCase;

class IdPParsingTest extends ItopTestCase
{
	
	public function setup(): void {
		parent::setup();
		require_once(dirname(__DIR__).'/src/Config.php');
	}
	
	/**
	 * Test that we are able to parse the meta information returned by a SimpleSAML Identity Provider
	 */
	public function testParseIdPSimpleSaml()
	{
		$sXML = file_get_contents(__DIR__.'/asset/simple-saml.xml');
		$aErrors = array();
		$aIdP = Config::ParseIdPMetaData($sXML, $aErrors);
		$this->assertEmpty($aErrors);
		$this->assertEquals($aIdP['entityId'], 'https://idp.test.com/simplesaml/saml2/idp/metadata.php');
		$this->assertArrayHasKey('x509certMulti', $aIdP);
		$this->assertEquals(count($aIdP['x509certMulti']), 2);
		$this->assertEquals(count($aIdP['singleSignOnService']), 2);
		$this->assertEquals($aIdP['singleSignOnService']['binding'], Config::BINDING_HTTP_REDIRECT);
		$this->assertEquals(count($aIdP['singleLogoutService']), 3);
		$this->assertEquals($aIdP['singleLogoutService']['binding'], Config::BINDING_HTTP_REDIRECT);
	}
	
	/**
	 * Test that we are able to parse the meta information returned by a Keycloak Identity Provider
	 */
	public function testParseIdPKeycloak()
	{
		$sXML = file_get_contents(__DIR__.'/asset/keycloak.xml');
		$aErrors = array();
		$aIdP = Config::ParseIdPMetaData($sXML, $aErrors);
		$this->assertEmpty($aErrors);

		$this->assertArrayHasKey('x509cert', $aIdP);
		$this->assertEquals(count($aIdP['singleSignOnService']), 2);
		$this->assertEquals($aIdP['singleSignOnService']['binding'], Config::BINDING_HTTP_REDIRECT);
		$this->assertEquals(count($aIdP['singleLogoutService']), 3);
		$this->assertEquals($aIdP['singleLogoutService']['binding'], Config::BINDING_HTTP_REDIRECT);
	}
}

