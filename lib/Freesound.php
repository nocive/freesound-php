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

if (! class_exists( 'Freesound_Bootstrap' )) {
	require_once( __DIR__ . '/Bootstrap/Bootstrap.php' );
}

/**
 * Main class
 *
 * @package Freesound
 */
class Freesound extends Freesound_Base
{
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
			$this->_config->Set( self::CFG_API_KEY, $apiKey );
		}

		$this->_interfaces = new StdClass();
		foreach( $this->_interfaceNames as $iname ) {
			$class = 'Freesound_API_' . ucfirst( strtolower( $iname ) );
			$this->_interfaces->{$iname} = new $class( $apiKey, $this->_config );
		}
	}


	public function Config()
	{
		return call_user_func_array( array( $this->_config, 'Set' ), func_get_args() );
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
