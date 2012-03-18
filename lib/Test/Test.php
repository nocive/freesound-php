<?php

class Freesound_Test
{
	protected $_params;
	protected $_class;

	public function __construct( $class, $params = null )
	{
		if (! is_object( $class )) {
			throw new InvalidArgumentException( '$class must be a valid object' );
		}

		$this->_params = $params;
		$this->_class = $class;

	}


	public function RunAll()
	{
		$r = array();
		foreach( get_object_vars( $this->_class->interfaces ) as $var => $value ) {
			foreach( get_class_methods( $value ) as $method ) {
				if (substr( $method, 0, 1 ) !== '_' ) {
					$classmethod = ucfirst( $var ) . $method;
					$args = ! empty( $this->_params[$classmethod] ) ? $this->_params[$classmethod] : array();
					$r[$classmethod] = call_user_func_array( array( $value, $method ), $args );
				}
			}
		}
		return $r;
	}


	// TODO implement __call for unit tests
}

?>
