<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

namespace Combodo\iTop\Extension\Saml;

use AbstractLoginFSMExtension;
use Dict;
use iLogoutExtension;
use LoginWebPage;
use OneLogin\Saml2\Auth;
use UserRights;
use utils;

class SAMLLoginExtension extends AbstractLoginFSMExtension implements iLogoutExtension
{
	private $bErrorOccurred = false;

	/**
	 * Return the list of supported login modes for this plugin
	 *
	 * @return array of supported login modes
	 */
	public function ListSupportedLoginModes()
	{
		return array('saml');
	}

	protected function OnReadCredentials(&$iErrorCode)
	{
		if (!isset($_SESSION['login_mode']) || ($_SESSION['login_mode'] == 'saml'))
		{
			$_SESSION['login_mode'] = 'saml';
			if (empty($_SESSION['auth_user']) && !$this->bErrorOccurred)
			{
				$oConfig = new Config();
				$oAuth = new Auth($oConfig->GetSettings());
				$oAuth->login(utils::GetAbsoluteUrlAppRoot().'pages/UI.php'); // Will redirect and exit
			}
		}
		return LoginWebPage::LOGIN_FSM_RETURN_CONTINUE;
	}

	protected function OnCheckCredentials(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			$sAuthUser = $_SESSION['auth_user'];
			if (!UserRights::CheckCredentials($sAuthUser, '', $_SESSION['login_mode'], 'external'))
			{
				$iErrorCode = LoginWebPage::EXIT_CODE_NOTAUTHORIZED;
				return LoginWebPage::LOGIN_FSM_RETURN_ERROR;
			}
		}
		return LoginWebPage::LOGIN_FSM_RETURN_CONTINUE;
	}

	protected function OnCredentialsOK(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			$sAuthUser = $_SESSION['auth_user'];
			LoginWebPage::OnLoginSuccess($sAuthUser, 'external', $_SESSION['login_mode']);
		}
		return LoginWebPage::LOGIN_FSM_RETURN_CONTINUE;
	}

	protected function OnError(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			echo "<p>".Dict::S('SAML:Error:UserNotAllowed')."</p>";
			exit();
		}
		return LoginWebPage::LOGIN_FSM_RETURN_CONTINUE;
	}

	protected function OnConnected(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			$_SESSION['can_logoff'] = true;
			return LoginWebPage::CheckLoggedUser($iErrorCode);
		}
		return LoginWebPage::LOGIN_FSM_RETURN_CONTINUE;
	}

	/**
	 * Execute all actions to log out properly
	 */
	public function LogoutAction()
	{
		$oConfig = new Config();
		$oAuth = new Auth($oConfig->GetSettings());
		$oAuth->logout(utils::GetAbsoluteUrlAppRoot().'pages/UI.php'); // Will redirect and exit
	}
}


class SAMLLoginExtension2
{
	public function GetSocialButtons()
	{
		return array(
			array(
				'login_mode' => 'saml',
				'label' => 'Login with SAML',
				//'twig' => 'saml_button.twig',
				'tooltip' => 'Here is a SAML specific tooltip',
			),
		);
	}
}