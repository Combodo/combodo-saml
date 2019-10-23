<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

namespace Combodo\iTop\Extension\Saml;

use AbstractLoginFSMExtension;
use Dict;
use iLoginUIExtension;
use iLogoutExtension;
use LoginBlockData;
use LoginTwigData;
use LoginWebPage;
use OneLogin\Saml2\Auth;
use utils;

class SAMLLoginExtension extends AbstractLoginFSMExtension implements iLogoutExtension, iLoginUIExtension
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
				if (!isset($_SESSION['login_will_redirect']))
				{
					$_SESSION['login_will_redirect'] = true;
				}
				else
				{
				    if (empty(utils::ReadParam('login_saml')))
                    {
                        unset($_SESSION['login_will_redirect']);
                        $this->bErrorOccurred = true;
                        $iErrorCode = LoginWebPage::EXIT_CODE_MISSINGLOGIN;
                        return LoginWebPage::LOGIN_FSM_ERROR;
                    }
				}
				$sOriginURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				if (!utils::StartsWith($sOriginURL, utils::GetAbsoluteUrlAppRoot()))
				{
					// If the found URL does not start with the configured AppRoot URL
					$sOriginURL = utils::GetAbsoluteUrlAppRoot().'pages/UI.php';
				}
				$oAuth->login($sOriginURL); // Will redirect and exit
			}
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
	}

	protected function OnCheckCredentials(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
            if (!isset($_SESSION['auth_user']))
            {
                $iErrorCode = LoginWebPage::EXIT_CODE_WRONGCREDENTIALS;
                return LoginWebPage::LOGIN_FSM_ERROR;
            }
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
	}

	protected function OnCredentialsOK(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			$sAuthUser = $_SESSION['auth_user'];
            if (!LoginWebPage::CheckUser($sAuthUser))
            {
                $iErrorCode = LoginWebPage::EXIT_CODE_NOTAUTHORIZED;
                return LoginWebPage::LOGIN_FSM_ERROR;
            }
			LoginWebPage::OnLoginSuccess($sAuthUser, 'external', $_SESSION['login_mode']);
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
	}

	protected function OnError(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			if ($iErrorCode != LoginWebPage::EXIT_CODE_MISSINGLOGIN)
			{
				$oLoginWebPage = new LoginWebPage();
				$oLoginWebPage->DisplayLogoutPage(false, Dict::S('SAML:Error:UserNotAllowed'));
                exit();
			}
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
	}

	protected function OnConnected(&$iErrorCode)
	{
		if ($_SESSION['login_mode'] == 'saml')
		{
			$_SESSION['can_logoff'] = true;
			return LoginWebPage::CheckLoggedUser($iErrorCode);
		}
		return LoginWebPage::LOGIN_FSM_CONTINUE;
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

    /**
     * @return LoginTwigData
     * @throws \Exception
     */
    public function GetTwigBlockData()
    {
        $sPath = APPROOT.'env-'.utils::GetCurrentEnvironment().'/combodo-saml/view';
        $oLoginData = new LoginTwigData(array(), $sPath);

        $aData = array(
            'sImagePath' => utils::GetAbsoluteUrlModulesRoot().'combodo-saml/view/saml.png',
            'sLoginMode' => 'saml',
            'sLabel' => Dict::S('SAML:Login:SignIn'),
            'sTooltip' => Dict::S('SAML:Login:SignInTooltip'),
        );
        $oBlockData = new LoginBlockData('saml_sso_button.html.twig', $aData);

        $oLoginData->AddBlockData('login_sso_buttons', $oBlockData);

        return $oLoginData;
    }
}

