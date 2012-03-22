<?php

/**
 * Base class for API classes
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
abstract class Freesound_API_Base extends Freesound_Base
{
	public $passedId;

	public static $interfaceNames = array(
		'sound',
		'user',
		'pack'
	);

	const PARAM_API_KEY = 'api_key';


	public function __construct( $apiKey = null, $config = null )
	{
		$this->_config = $config !== null ? $config : new Freesound_Config();
		if ($apiKey !== null) {
			$this->_config->Set( self::CFG_API_KEY, $apiKey );
		}
	}


	public function __destruct()
	{
	}


	protected function _PrepareParams( $params = array() )
	{
		if ($this->passedId !== null) {
			end( $params );
			while ( ! is_null( $key = key( $params )) ) {
				$val = current( $params );
				$elem = prev( $params );
				$params[$key] = $elem;
			}
			reset( $params );
			$params[key( $params )] = $this->passedId;
			$this->passedId = null;
		}
		return $params;
	}


	protected function _RequestUrl( $method, $args = null, $extraArgs = null )
	{
		$apiKey = $this->_config->Get( self::CFG_API_KEY );
		if (empty( $apiKey )) {
			throw new Exception( 'API key not set or empty' );
		}

		$constUrl = 'static::URL_' . strtoupper( $method );
		if (! defined( $constUrl )) {
			throw new InvalidArgumentException( "No such API method '$method'" );
		}

		$url = vsprintf( preg_replace( '/<[\w_]+>/', '%s', constant( $constUrl ) ), $args );
		$extraArgs = is_array( $extraArgs ) ? $extraArgs: array();
		$queryString = http_build_query( array_merge( $extraArgs, array( self::PARAM_API_KEY => $apiKey ) ) );

		return rtrim( self::URL_BASE, '/' ) . '/' . ltrim( $url, '/' ) . '?' . $queryString;
	}


	protected function _Request( $method, $args = null, $extraArgs = null )
	{
		$start = microtime( true );

		$cfg = $this->_config->Get(); // cache it
		$url = $this->_RequestUrl( $method, $args, $extraArgs );
		
		if ($cfg[self::CFG_DEBUG]) {
			echo "Requesting: $url... ";
		}

		$curlopts = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_VERBOSE => $cfg[self::CFG_DEBUG] > 1,
			CURLOPT_USERAGENT => isset( $cfg[self::CFG_FETCH_USER_AGENT] ) ? $cfg[self::CFG_FETCH_USER_AGENT] : ini_get( 'user_agent' )
		);

		$c = curl_init();
		
		curl_setopt_array( $c, $curlopts );
		if (isset( $cfg[self::CFG_FETCH_CONNECT_TIMEOUT] )) {
			curl_setopt( $c, CURLOPT_CONNECTTIMEOUT, $cfg[self::CFG_FETCH_CONNECT_TIMEOUT] );
		}
		if (isset( $cfg[self::CFG_FETCH_TIMEOUT] )) {
			curl_setopt( $c, CURLOPT_TIMEOUT, $cfg[self::CFG_FETCH_TIMEOUT] );
		}
		
		$response = curl_exec( $c );
		$httpCode = curl_getinfo( $c, CURLINFO_HTTP_CODE );
		curl_close( $c );

		if ($cfg[self::CFG_DEBUG]) {
			echo "took: " . round( microtime( true ) - $start, 2 ) . "s\n";
		}

		if ($response === false) {
			throw new Freesound_CommunicationException( "Error contacting the freesound API. HTTP Code: $httpCode" );
		}

		$response = json_decode( $response, $cfg[self::CFG_JSON_DECODE_ASSOC] );
		if ($response === false) {
			throw new Freesound_MalformedResponseException( 'Error parsing the freesound API response' );
		}

		if ($httpCode !== 200) {
			throw new Freesound_APIErrorException( "API error, response: " . print_r( $response, true ) );
		}

		return $response;
	}
}

?>
