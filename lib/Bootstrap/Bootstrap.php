<?php

/**
 * Bootstrap class
 *
 * @package    Freesound
 * @subpackage Freesound_Bootstrap
 */
class Freesound_Bootstrap
{
	public static $classmap = array(
		'Freesound' => 'Freesound',
		'Freesound_Base' => 'Base/Base',
		'Freesound_Bootstrap' => 'Bootstrap/Bootstrap',
		'Freesound_Config' => 'Config/Config',
		'Freesound_Utils' => 'Utils/Utils',
		'Freesound_API_Base' => 'API/Base',
		'Freesound_API_Pack' => 'API/Pack',
		'Freesound_API_Sound' => 'API/Sound',
		'Freesound_API_User' => 'API/User',
		'Freesound_CommunicationException' => 'Exception/Exception',
		'Freesound_MalformedResponseException' => 'Exception/Exception',
		'Freesound_APIErrorException' => 'Exception/Exception'
	);

	const CLASS_EXTENSION = '.php';


	public static function Autoload( $class )
	{
		if (isset( self::$classmap[$class] )) {
			$classFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, self::$classmap[$class] ) . self::CLASS_EXTENSION;
			include_once( $classFile );
		}
	}


	public static function AutoloadRegister()
	{
		spl_autoload_register( __CLASS__ . '::Autoload' );
	}
}

if (! defined( '__FREESOUND_BUNDLE__' )) {
	Freesound_Bootstrap::AutoloadRegister();
}

?>
