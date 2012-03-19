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
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version  0.1
 * @see      http://www.freesound.org/docs/api/overview.html
 * @see      http://www.freesound.org/docs/api/resources.html#resources
 * @see      https://github.com/ffont/freesound-javascript/blob/master/freesoundLib.js
 * @link     https://github.com/nocive/freesound-php/
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
	public $interfaces;


	public function __construct( $apiKey = null, $config = null )
	{
		$this->_config = new Freesound_Config( $config );
		if ($apiKey !== null) {
			$this->_config->Set( self::CFG_API_KEY, $apiKey );
		}

		$this->interfaces = new StdClass();
		foreach( Freesound_API_Base::$interfaceNames as $iname ) {
			$class = 'Freesound_API_' . ucfirst( strtolower( $iname ) );
			$this->interfaces->{$iname} = new $class( $apiKey, $this->_config );
		}
	}


	public function Config()
	{
		return call_user_func_array( array( $this->_config, 'Set' ), func_get_args() );
	}


	public function __call( $method, $args )
	{
		foreach( get_object_vars( $this->interfaces ) as $var => $value ) {
			if (strtolower( $method ) === $var) {
				if (count( $args ) === 1) {
					$this->interfaces->{$var}->passedId = $args[0];
				}
				return $this->interfaces->{$var};
			}

			$realMethod = substr( $method, strlen( $var ) );
			if (stripos( $method, $var ) === 0 && method_exists( $this->interfaces->{$var}, $realMethod )) {
				return call_user_func_array( array( $this->interfaces->{$var}, $realMethod ), $args );
			}
		}

		throw new Exception( "Invalid method: $method" );
	}
}

?>
