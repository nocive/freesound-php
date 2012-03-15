<?php

/*************************************************************************************************
 *
 * Freesound API client library
 * PHP library for interacting with the Freesound.org API
 *
 * @see http://www.freesound.org/docs/api/overview.html
 * @see http://www.freesound.org/docs/api/resources.html#resources
 * @see https://github.com/ffont/freesound-javascript/blob/master/freesoundLib.js
 *
 * @author Jose' Pedro Saraiva <nocive _ gmail _ com>
 *
 *************************************************************************************************/


/**
 * Base class
 *
 * @package    Freesound
 */
class Freesound_Base
{
	protected $_config = array();

	const VERSION = '0.1';
	const URL_BASE = 'http://www.freesound.org/api/';

	const CFG_API_KEY = 'api_key';
	const CFG_DEBUG = 'debug';
	const CFG_FETCH_CONNECT_TIMEOUT = 'fetch_connect_timeout';
	const CFG_FETCH_TIMEOUT = 'fetch_timeout';
	const CFG_FETCH_USER_AGENT = 'fetch_user_agent';
	const CFG_JSON_DECODE_ASSOC = 'json_decode_assoc';

	const PARAM_API_KEY = 'api_key';
}


/**
 * Config class
 *
 * @package    Freesound
 * @subpackage Freesound_Config
 */
class Freesound_Config extends Freesound_Base
{
	protected static $_defaults = array(
		self::CFG_API_KEY => '',
		self::CFG_DEBUG => 0,
		self::CFG_FETCH_CONNECT_TIMEOUT => 30,
		self::CFG_FETCH_TIMEOUT => 30,
		self::CFG_FETCH_USER_AGENT => 'Freesound API PHP client v%VERSION%',
		self::CFG_JSON_DECODE_ASSOC => false
	);


	public function __construct( $cfg = null )
	{
		$cfg = $cfg !== null ? array_merge( self::$_defaults, $cfg ) : self::$_defaults;
		$this->set( $cfg );
	}


	public function get()
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


	public function set()
	{
		$args = func_get_args();
		$argc = func_num_args();

		if ($argc !== 1 && $argc !== 2) {
			throw new InvalidArgumentException( 'Wrong number of parameters' );
		}

		if ($argc === 1 && is_array( $args[0] )) {
			$vars = array_combine( array_keys( $args[0] ), array_values( $args[0] ) );
		} elseif ($argc === 2) {
			$vars = array_combine( array( $args[0] ), array( $args[1] ) );
		}

		foreach ( $vars as $var => $value ) {
			if (array_key_exists( $var, self::$_defaults )) {
				if ($var === self::CFG_FETCH_USER_AGENT) {
					$value = str_replace( '%VERSION%', self::VERSION, $value );
				}
				$this->_config[$var] = $value;
			}
		}
	}


	public function reset()
	{
		$this->set( self::$_defaults );
	}	
}


/**
 * Base class for API classes
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class FreesoundAPI_Base extends Freesound_Base
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
			throw new FreesoundCommunicationException( "Error contacting the freesound API. HTTP Code: $httpCode" );
		}

		$response = json_decode( $response, $cfg[self::CFG_JSON_DECODE_ASSOC] );
		if ($response === false) {
			throw new FreesoundMalformedResponseException( 'Error parsing the freesound API response' );
		}

		if ($httpCode !== 200) {
			throw new FreesoundAPIErrorException( "API error, details: " . print_r( $response, true ) );
		}

		return $response;
	}
}


/**
 * Sound class
 *
 * @package    Freesound
 * @subpackage FreesoundAPI
 */
class FreesoundAPI_Sound extends FreesoundAPI_Base
{
	const URL_SOUND = '/sounds/<sound_id>/';
	const URL_SOUND_ANALYSIS = '/sounds/<sound_id>/analysis/<filter>/';
	const URL_SOUND_ANALYSIS_NO_FILTER = '/sounds/<sound_id>/analysis/';
	const URL_SOUND_GEOTAG = '/sounds/geotag/';
	const URL_SOUND_SIMILAR = '/sounds/<sound_id>/similar/';
	const URL_SOUND_SEARCH = '/sounds/search/';


	public function Get( $id )
	{
		return $this->_request( 'sound', $id );
	}


	public function GetAnalysis( $id, $filter = null, $all = false )
	{
		if (empty( $filter )) {
			$filter = false;
			$method = 'sound_analysis_no_filter';
		} else {
			$method = 'sound_analysis';
			if (is_array( $filter )) {
				$filter = implode( '/', $filter );
			}
		}

		return $this->_request( $method, array( $id, $filter ), array(
			'all' => $all
		));
	}


	public function GetSimilar( $id, $num = null, $preset = null, $fields = null )
	{
		return $this->_request( 'sound_similar', $id, array(
			'num_results' => $num,
			'preset' => $preset,
			'fields' => $fields
		));
	}


	public function Search( $query, $page = null, $filter = null, $sort = null, $fields = null )
	{
		return $this->_request( 'sound_search', null, array(
			'q' => $query,
			'p' => $page,
			'f' => $filter,
			's' => $sort,
			'fields' => $fields
		));
	}


	public function SearchGeo( $minLat = null, $maxLat = null, $minLon = null, $maxLon = null, $page = null, $fields = null )
	{
		return $this->_request( 'sound_geotag', null, array(
			'min_lat' => $minLat,
			'max_lat' => $maxLat,
			'min_lon' => $minLon,
			'max_lon' => $maxLon,
			'p' => $page,
			'fields' => $fields
		));
	}


}


/**
 * User class
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class FreesoundAPI_User extends FreesoundAPI_Base
{
	const URL_USER = '/people/<user_name>/';
	const URL_USER_SOUNDS = '/people/<user_name>/sounds/';
	const URL_USER_PACKS = '/people/<user_name>/packs/';


	public function Get( $username )
	{
		return $this->_request( 'user', $username );
	}


	public function GetSounds( $username )
	{
		return $this->_request( 'user_sounds', $username );
	}


	public function GetPacks( $username )
	{
		return $this->_request( 'user_packs', $username );
	}
}


/**
 * Pack class
 *
 * @package    Freesound
 * @subpackage FreesoundAPI
 */
class FreesoundAPI_Pack extends FreesoundAPI_Base
{
	const URL_PACK = '/packs/<pack_id>/';
	const URL_PACK_SOUNDS = '/packs/<pack_id>/sounds/';


	public function Get( $id )
	{
		return $this->_request( 'pack', $id );
	}


	public function GetSounds( $id )
	{
		return $this->_request( 'pack_sounds', $id );
	}
}


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
			$class = 'FreesoundAPI_' . ucfirst( strtolower( $iname ) );
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

class FreesoundCommunicationException extends Exception {}
class FreesoundMalformedResponseException extends Exception {}
class FreesoundAPIErrorException extends Exception {}


//$fs = new Freesound();
$fs = new Freesound( '77bbf1a63bc84ccc9d80a38d6345ef60' );

/*$fs->config( array(
	'api_key' => '77bbf1a63bc84ccc9d80a38d6345ef60', 
	'fetch_connect_timeout' => 30, 
	'fetch_timeout' => 20, 
	'debug' => 1,
	'json_decode_assoc' => false
));*/

$fs->config( 'debug', 1 );


//$r = $fs->SoundSearch( 'foo' );
$r = $fs->Sound()->Search( 'foo' );

//$r = $fs->SoundGet( 120597 );
$r = $fs->Sound()->Get( 120597 );

//$r = $fs->SoundSearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );
$r = $fs->Sound()->SearchGeo( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );

//$r = $fs->SoundGetAnalysis( 120597 );
$r = $fs->Sound()->GetAnalysis( 120597 );

//$r = $fs->SoundGetSimilar( 120597 );
$r = $fs->Sound()->GetSimilar( 120597 );

//$r = $fs->UserGet( 'artshare' );
$r = $fs->User()->Get( 'artshare' );

//$r = $fs->UserGetSounds( 'artshare' );
$r = $fs->User()->GetSounds( 'artshare' );

//$r = $fs->UserGetPacks( 'artshare' );
$r = $fs->User()->GetPacks( 'artshare' );

//$r = $fs->PackGet( 5107 );
$r = $fs->Pack()->Get( 5107 );

//$r = $fs->PackGetSounds( 5107 );
$r = $fs->Pack()->GetSounds( 5107 );

//var_dump( $r );

?>
