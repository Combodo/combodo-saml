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

        parent::Enable($sTargetFile);
    }

    public static function Log($sLevel, $sMessage, $sChannel = null, $aContext = array())
    {
        if (! static::$m_oFileLog) {
            self::Enable();
        }

        parent::Log($sLevel, $sMessage, $sChannel, $aContext);
    }
}