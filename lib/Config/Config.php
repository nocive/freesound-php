<?php

/**
 * Config class
 *
 * @package    Freesound
 * @subpackage Freesound_Config
 */
class Freesound_Config extends Freesound_Base
{
	protected $_config = array();
	
	protected static $_defaults = array(
		self::CFG_API_KEY => '',
		self::CFG_DEBUG => 0,
		self::CFG_FETCH_CONNECT_TIMEOUT => 30,
		self::CFG_FETCH_TIMEOUT => 30,
		self::CFG_FETCH_USER_AGENT => 'Freesound API PHP client v%VERSION% (%WEBSITE%)',
		self::CFG_JSON_DECODE_ASSOC => false
	);


	public function __construct( $cfg = null )
	{
		$cfg = $cfg !== null ? array_merge( self::$_defaults, $cfg ) : self::$_defaults;
		$this->Set( $cfg );
	}


	public function Get()
	{
		if (func_num_args() === 0) {
			return $this->_config;
		}

		$args = func_get_args();
		if (func_num_args() === 1) {
			if (is_array( $args[0] )) {
				$args = $args[0];
			} else {
				return $this->_config[$args[0]];
			}
		}

		$return = array();
		foreach ( $args as $a ) {
			$return[$a] = $this->_config[$a];
		}
		return $return;
	}


	public function Set()
	{
		$args = func_get_args();
		$argc = func_num_args();

		if ($argc !== 1 && $argc !== 2) {
			throw new InvalidArgumentException( 'Wrong number of parameters' );
		}

		if ($argc === 1 && is_array( $args[0] )) {
			$vars = $args[0];
		} elseif ($argc === 2) {
			$vars = array_combine( array( $args[0] ), array( $args[1] ) );
		}

		foreach ( $vars as $var => $value ) {
			if (array_key_exists( $var, self::$_defaults )) {
				if ($var === self::CFG_FETCH_USER_AGENT) {
					$value = str_replace( array( '%VERSION%', '%WEBSITE%' ), array( self::VERSION, self::WEBSITE ), $value );
				}
				$this->_config[$var] = $value;
			}
		}
	}


	public function Reset()
	{
		$this->Set( self::$_defaults );
	}	
}

?>
