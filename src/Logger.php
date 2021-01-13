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
class Logger
{
	const ERROR = 'Error';
	const WARNING = 'Warning';
	const INFO = 'Info';
	const DEBUG = 'Debug';
	const TRACE = 'Trace';

    private static $bDebug = null;
    private static $bTrace = null;

    private static function Log($sLogLevel, $sMessage)
	{
		if (static::$bDebug === null) {
			static::$bDebug = MetaModel::GetModuleSetting('combodo-saml', 'debug', true);
		}
		
		if ((!static::$bDebug) && ($sLogLevel != static::ERROR)) {
			// If not in debug mode, log only ERROR messages
			return;
		}

		if ($sLogLevel == static::TRACE) {
            if (static::$bTrace === null) {
                // contrary to the other level of logging, the traces can leak sensible information, do not keep them enabled
                // this is why they are not enabled like the other one by the 'debug' setting.
                static::$bTrace = MetaModel::GetModuleSetting('combodo-saml', 'trace', true);
            }
            if (!static::$bTrace) {

                return;
            }
        }

		
		$sLogFile = APPROOT.'/log/saml.log';
		
		$hLogFile = fopen($sLogFile, 'a');
		if ($hLogFile !== false)
		{
			flock($hLogFile, LOCK_EX);
			$sDate = date('Y-m-d H:i:s');
			fwrite($hLogFile, "$sDate | $sLogLevel | $sMessage\n");
			fflush($hLogFile);
			flock($hLogFile, LOCK_UN);
			fclose($hLogFile);
		}
		else
		{
			IssueLog::Error("Cannot open log file '$sLogFile' for writing.");
			IssueLog::Info($sMessage);
		}
	}
	
	public static function Error($sMessage)
	{
		static::Log(static::ERROR, $sMessage);
	}

	
	public static function Warning($sMessage)
	{
		static::Log(static::WARNING, $sMessage);
	}
	
	public static function Info($sMessage)
	{
		static::Log(static::INFO, $sMessage);
	}
	
	public static function Debug($sMessage)
	{
		static::Log(static::DEBUG, $sMessage);
	}

    public static function Trace($sMessage)
    {
        static::Log(static::TRACE, $sMessage);
    }
}