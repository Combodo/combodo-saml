<?php
/**
 * @copyright   Copyright (C) 2020 Combodo SARL
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

namespace Combodo\iTop\Extension\Saml;
use MetaModel;

/**
 *  Simple logger to write to log/saml.log
 */
class Logger extends \LogAPI
{
    const CHANNEL_DEFAULT   = 'combodoSaml';

    protected static $m_oFileLog = null;

    public static function Enable($sTargetFile = null)
    {
        if (empty($sTargetFile))
        {
            $sTargetFile = APPROOT.'log/saml.log';
        }

        self::legacyConfHandler();

        parent::Enable($sTargetFile);
    }

    public static function Log($sLevel, $sMessage, $sChannel = null, $aContext = array())
    {
        if (! static::$m_oFileLog) {
            self::Enable();
        }

        parent::Log($sLevel, $sMessage, $sChannel, $aContext);
    }

    private static function legacyConfHandler()
    {
        if (static::$m_oFileLog) {
            return;
        }

        $oConfig = (static::$m_oMockMetaModelConfig !== null) ? static::$m_oMockMetaModelConfig :  \MetaModel::GetConfig();
        if (!$oConfig instanceof Config)
        {
            return;
        }

        $deprecatedSettingValue = static::$bDebug = MetaModel::GetModuleSetting('combodo-saml', 'debug', null);
        if ($deprecatedSettingValue !== false) {
            //we only handle the legacy behavior of when the loggin was disabled it did still log errors.
            return;
        }

        $sLogLevelMin = $oConfig->Get('log_level_min');

//        if (isset($sLogLevelMin[static::CHANNEL_DEFAULT]) && $deprecatedSettingValue == null)
//        {
//            //This is the nominal case
//            return;
//        }

        if (
            (false == $deprecatedSettingValue) &&
            (
                (!isset($sLogLevelMin[static::CHANNEL_DEFAULT]))
                ||
                (static::LEVEL_ERROR != $sLogLevelMin[static::CHANNEL_DEFAULT])
            )
        )
        {
            //the legacy code was filtering out the log below Error  of the legacy setting was false
            \IssueLog::Warning('The config "debug" for module "combodo-saml" is deprecated, you should use the "log_level_min" for the channel "'.self::CHANNEL_DEFAULT.'" instead.');
            $sLogLevelMin[static::CHANNEL_DEFAULT] = static::LEVEL_ERROR;
            $oConfig->Set('log_level_min', $sLogLevelMin);
            return;
        }

//        if (! isset($sLogLevelMin[static::CHANNEL_DEFAULT]) && $deprecatedSettingValue != false)
//        {
//            //Per default, the legacy code was allowing the debug level, this is not the standard behavior, but, well, we choose to be futur proof and do not hjandle this speicficity
//            return;
//        }
    }
}