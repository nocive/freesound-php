<?php

/*************************************************************************************************
 *
 * Freesound API client library
 * PHP library for interacting with the Freesound.org API
 *
 * @author Jose' Pedro Saraiva <nocive _ gmail _ com>
 *
 * @see http://www.freesound.org/docs/api/overview.html
 * @see http://www.freesound.org/docs/api/resources.html#resources
 * @see https://github.com/ffont/freesound-javascript/blob/master/freesoundLib.js
 *
 *************************************************************************************************/


/**
 * Autoloader
 *
 * @package Freesound
 */
spl_autoload_register(function( $class ) {
	static $classmap = array(
		'Freesound_API_Base' => 'API/Base',
		'Freesound_API_Pack' => 'API/Pack',
		'Freesound_API_Sound' => 'API/Sound',
		'Freesound_API_User' => 'API/User',
		'Freesound_Base' => 'Base/Base',
		'Freesound_Config' => 'Config/Config',
		'Freesound_CommunicationException' => 'Exception/Exception',
		'Freesound_MalformedResponseException' => 'Exception/Exception',
		'Freesound_APIErrorException' => 'Exception/Exception'
	);

	if (isset( $classmap[$class] )) {
		$classFile = str_replace( '/', DIRECTORY_SEPARATOR, $classmap[$class] ) . '.php';
		include_once( $classFile );
	}
});


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
