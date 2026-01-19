<?php
/**
 * @copyright   Copyright (C) 2024 Combodo SAS
 * @license     https://www.combodo.com/documentation/combodo-software-license.html
 *
 */

namespace Combodo\iTop\Extension\Saml;
use IssueLog;
use MetaModel;

/**
 *  Simple logger to write to log/saml.log
 */
class Logger extends IssueLog
{
	public const CHANNEL_DEFAULT = 'SAML';
	public const LEVEL_DEFAULT = self::LEVEL_ERROR;

    private static $bDebug = null;

	public static function GetMinLogLevel($sChannel, $sConfigKey = self::ENUM_CONFIG_PARAM_FILE)
	{
		if ($sChannel === static::CHANNEL_DEFAULT)
		{
			if (static::$bDebug === null)
			{
				static::$bDebug = MetaModel::GetModuleSetting('combodo-saml', 'debug', false);
			}

			if (static::$bDebug)
			{
				return static::LEVEL_TRACE;
			}
		}

		return parent::GetMinLogLevel($sChannel, $sConfigKey);
	}
}
