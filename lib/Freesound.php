<?php

/*************************************************************************************************
 *
 * Freesound (http://freesound.org) API client library
 *
 * PHP library for interacting with the Freesound.org API
 *
 * PHP >= 5.3
 *
 * @category API Client
 * @package  Freesound
 * @author   Jose' Pedro Saraiva <nocive _ gmail _ com>
 * @license  GPL-3.0 http://www.opensource.org/licenses/GPL-3.0
 * @version  0.1
 * @see      http://www.freesound.org/docs/api/overview.html
 * @see      http://www.freesound.org/docs/api/resources.html#resources
 * @see      https://github.com/ffont/freesound-javascript/blob/master/freesoundLib.js
 *
 *************************************************************************************************/

/**
 * Autoloader class
 *
 * @package Freesound
 */
class Freesound_Utils
{
	public static $classmap = array(
		'Freesound_Base' => 'Base/Base',
		'Freesound_Config' => 'Config/Config',
		'Freesound_API_Base' => 'API/Base',
		'Freesound_API_Pack' => 'API/Pack',
		'Freesound_API_Sound' => 'API/Sound',
		'Freesound_API_User' => 'API/User',
		'Freesound_CommunicationException' => 'Exception/Exception',
		'Freesound_MalformedResponseException' => 'Exception/Exception',
		'Freesound_APIErrorException' => 'Exception/Exception'
	);

	const CLASS_EXTENSION = '.php';


	public static function BundleBuild( $file )
	{
		if (is_file( $file )) {
			throw new Exception( "File '$file' already exists, please remove it before trying to build a new bundle" );
		}

		$classpaths = array_unique( array_values( self::$classmap ) );

		$content = "<?php\n\n";
		foreach( $classpaths as $path ) {
			$f = __DIR__ . DIRECTORY_SEPARATOR . $path . self::CLASS_EXTENSION;
			if (false === ($c = @file_get_contents( $f ))) {
				throw new Exception( "Could not open file '$f'" );
			}
			// remove php tags
			$c = preg_replace ( '#<\?(?:php)?\s*(.*?)\s*\?>#s', '\\1', $c );
			$content .= "$c\n\n";
		}
		$content .= "?>";

		return @file_put_contents( $file, $content ) !== false;
	}


	public static function Autoload( $class )
	{
		if (isset( self::$classmap[$class] )) {
			$classFile = str_replace( '/', DIRECTORY_SEPARATOR, self::$classmap[$class] ) . self::CLASS_EXTENSION;
			include_once( $classFile );
		}
	}


	public static function AutoloadRegister()
	{
		spl_autoload_register( __CLASS__ . '::Autoload' );
	}
}

// register autoload
Freesound_Utils::AutoloadRegister();


/**
 * Main class
 *
 * @package Freesound
 */
class Freesound extends Freesound_Base
{
	public $_config;

	protected $_interfaces;

	protected $_interfaceNames = array(
		'sound',
		'user',
		'pack'
	);


	public function __construct( $apiKey = null, $config = null )
	{
		$this->_config = $config !== null ? $config : new Freesound_Config();
		if ($apiKey !== null) {
			$this->_config->set( self::CFG_API_KEY, $apiKey );
		}

		$this->_interfaces = new StdClass();
		foreach( $this->_interfaceNames as $iname ) {
			$class = 'Freesound_API_' . ucfirst( strtolower( $iname ) );
			$this->_interfaces->{$iname} = new $class( $apiKey, $this->_config );
		}
	}


	public function Config()
	{
		return call_user_func_array( array( $this->_config, 'set' ), func_get_args() );
	}


	public function __call( $method, $args )
	{
		foreach( get_object_vars( $this->_interfaces ) as $var => $value ) {
			if (strtolower( $method ) === $var) {
				return $this->_interfaces->{$var};
			}

			$realMethod = substr( $method, strlen( $var ) );
			if (stripos( $method, $var ) === 0 && method_exists( $this->_interfaces->{$var}, $realMethod )) {
				return call_user_func_array( array( $this->_interfaces->{$var}, $realMethod ), $args );
			}
		}

		throw new Exception( "Invalid method: $method" );
	}
}

?>
