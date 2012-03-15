<?php

/**
 * Base class for API classes
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class Freesound_API_Base extends Freesound_Base
{
	public function __construct( $apiKey = null, $config = null )
	{
		$this->_config = $config !== null ? $config : new Freesound_Config();
		if ($apiKey !== null) {
			$this->_config->set( self::CFG_API_KEY, $apiKey );
		}
	}


	public function __destruct()
	{
	}


	protected function _requestUrl( $method, $args = null, $extraArgs = null )
	{
		$apiKey = $this->_config->get( self::CFG_API_KEY );
		if (empty( $apiKey )) {
			throw new Exception( 'API key not set or empty' );
		}

		$constUrl = 'static::URL_' . strtoupper( $method );
		if (! defined( $constUrl )) {
			throw new InvalidArgumentException( "No such API method '$method'" );
		}
		$url = constant( $constUrl );
		foreach( (array) $args as $a ) {
			$url = preg_replace( '/<[\w_]+>/', $a, $url, 1 );
		}

		$extraArgs = is_array( $extraArgs ) ? $extraArgs: array();
		$queryString = http_build_query( array_merge( $extraArgs, array( self::PARAM_API_KEY => $apiKey ) ) );
		return rtrim( self::URL_BASE, '/' ) . '/' . ltrim( $url, '/' ) . '?' . $queryString;
	}


	protected function _request( $method, $args = null, $extraArgs = null )
	{
		$start = microtime( true );

		$cfg = $this->_config->get(); // cache it
		$url = $this->_requestUrl( $method, $args, $extraArgs );

		$curlopts = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_VERBOSE => $cfg[self::CFG_DEBUG] > 1 ? 1 : 0,
			CURLOPT_CONNECTTIMEOUT => isset( $cfg[self::CFG_FETCH_CONNECT_TIMEOUT] ) ? $cfg[self::CFG_FETCH_CONNECT_TIMEOUT] : 20,
			CURLOPT_TIMEOUT => isset( $cfg[self::CFG_FETCH_TIMEOUT] ) ? $cfg[self::CFG_FETCH_TIMEOUT] : 30,
			CURLOPT_USERAGENT => isset( $cfg[self::CFG_FETCH_USER_AGENT] ) ? $cfg[self::CFG_FETCH_USER_AGENT] : ini_get( 'user_agent' )
		);

		if ($cfg[self::CFG_DEBUG]) {
			echo "Requesting: $url... ";
		}

		$c = curl_init();
		curl_setopt_array( $c, $curlopts );
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
			throw new Freesound_APIErrorException( "API error, details: " . print_r( $response, true ) );
		}

		return $response;
	}
}

?>
