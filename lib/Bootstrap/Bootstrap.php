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
		'Freesound_Test' => 'Test/Test',
		'Freesound_CommunicationException' => 'Exception/Exception',
		'Freesound_MalformedResponseException' => 'Exception/Exception',
		'Freesound_APIErrorException' => 'Exception/Exception'
	);

	const CLASS_EXTENSION = '.php';


	public static function Autoload( $class )
	{
		if (isset( self::$classmap[$class] )) {
			include_once( __DIR__ . '/../' . self::$classmap[$class] . self::CLASS_EXTENSION );
		}
	}


	public static function AutoloadRegister()
	{
		spl_autoload_register( __CLASS__ . '::Autoload' );
	}
}

if (! class_exists( 'Freesound' )) {
	Freesound_Bootstrap::AutoloadRegister();
}

?>
