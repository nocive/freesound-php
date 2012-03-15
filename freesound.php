<?php

/**
 * Freesound API
 * PHP library for interacting with the Freesound.org API
 *
 * @see http://www.freesound.org/docs/api/overview.html
 * @see http://www.freesound.org/docs/api/resources.html#resources
 * @see https://github.com/ffont/freesound-javascript/blob/master/freesoundLib.js
 *
 * @author Jose' Pedro Saraiva <nocive _ gmail _ com>
 */


class FreesoundAPI_Base
{
	public $debug = false;

	public $apiKey;

	protected $_curlOptions = array(
		'timeout' => 30,
		'connect_timeout' => 20,
		'user_agent' => 'Freesound PHP API client'
	);

	// TODO turn this into constants
	protected static $_baseUrl = 'http://www.freesound.org/api/';

	protected static $_urls = array(
		'sound' => '/sounds/<sound_id>/',
		'sound_analysis' => '/sounds/<sound_id>/analysis/<filter>/',
		'sound_analysis_no_filter' => '/sounds/<sound_id>/analysis/',
		'sound_geotag' => '/sounds/geotag/',
		'similar_sounds' => '/sounds/<sound_id>/similar/',
		'search' => '/sounds/search/',
		'user' => '/people/<user_name>/',
		'user_sounds' => '/people/<user_name>/sounds/',
		'user_packs' => '/people/<user_name>/packs/',
		'pack' => '/packs/<pack_id>/',
		'pack_sounds' => '/packs/<pack_id>/sounds/'
	);



	public function __construct( $apiKey = null )
	{
		if ($apiKey !== null) {
			$this->apiKey = $apiKey;
		}
	}


	public function __destruct()
	{
	}


	public function SetCurlOptions( $opts )
	{
		$this->_curlOptions = $this->_curlOptions + $opts;
	}


	protected function _requestUrl( $method, $args = null, $extraArgs = null )
	{
		if (empty( $this->apiKey )) {
			throw new Exception( 'Empty API key. Use setApiKey() or pass it in the class constructor' );
		}

		if (! array_key_exists( $method, self::$_urls )) {
			throw new InvalidArgumentException( "No such API method '$method'" );
		}

		$url = self::$_urls[$method];
		foreach( (array) $args as $a ) {
			$url = preg_replace( '/<[\w_]+>/', $a, $url, 1 );
		}

		$extraArgs = is_array( $extraArgs ) ? $extraArgs: array();
		$queryString = http_build_query( array_merge( $extraArgs, array( 'api_key' => $this->apiKey ) ) );

		return rtrim( self::$_baseUrl, '/' ) . '/' . ltrim( $url, '/' ) . '?' . $queryString;
	}


	protected function _request( $url )
	{
		$c = curl_init();

		$curlopts = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_VERBOSE => $this->debug ? 1 : 0,
			CURLOPT_CONNECTTIMEOUT => $this->_curlOptions['connect_timeout'],
			CURLOPT_TIMEOUT => $this->_curlOptions['timeout'],
			CURLOPT_USERAGENT => $this->_curlOptions['user_agent']
		);

		curl_setopt_array( $c, $curlopts );

		$response = curl_exec( $c );
		$httpCode = curl_getinfo( $c, CURLINFO_HTTP_CODE );
		curl_close( $c );

		if ($response === false) {
			throw new FreesoundCommunicationException( "Error contacting the freesound API. HTTP Code: $httpCode" );
		}

		$response = json_decode( $response );
		if ($response === false) {
			throw new FreesoundMalformedResponseException( 'Error parsing the freesound API response' );
		}

		if ($httpCode !== 200) {
			throw new FreesoundAPIErrorException( "API error, details: " . print_r( $response, true ) );
		}

		return $response;
	}
}


class FreesoundAPI_Sound extends FreesoundAPI_Base
{
	public function Get( $id )
	{
		$response = $this->_request( $this->_requestUrl( 'sound', $id ) );
		return $response;
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

		$response = $this->_request( $this->_requestUrl( $method, array( $id, $filter ), array(
			'all' => $all
		)));
		return $response;
	}


	public function GetSimilar( $id, $num = null, $preset = null, $fields = null )
	{
		$response = $this->_request( $this->_requestUrl( 'similar_sounds', $id, array(
			'num_results' => $num,
			'preset' => $preset,
			'fields' => $fields
		)));
		return $response;
	}


	public function Search( $query, $page = null, $filter = null, $sort = null, $fields = null )
	{
		$response = $this->_request( $this->_requestUrl( 'search', null, array(
			'q' => $query,
			'p' => $page,
			'f' => $filter,
			's' => $sort,
			'fields' => $fields
		)));
		return $response;
	}


	public function SearchGeo( $minLat = null, $maxLat = null, $minLon = null, $maxLon = null, $page = null, $fields = null )
	{
		$response = $this->_request( $this->_requestUrl( 'sound_geotag', null, array(
			'min_lat' => $minLat,
			'max_lat' => $maxLat,
			'min_lon' => $minLon,
			'max_lon' => $maxLon,
			'p' => $page,
			'fields' => $fields
		)));
		return $response;
	}


}


class FreesoundAPI_User extends FreesoundAPI_Base
{
	public function Get( $username )
	{
		$response = $this->_request( $this->_requestUrl( 'user', $username ) );
		return $response;
	}


	public function GetSounds( $username )
	{
		$response = $this->_request( $this->_requestUrl( 'user_sounds', $username ) );
		return $response;
	}


	public function GetPacks( $username )
	{
		$response = $this->_request( $this->_requestUrl( 'user_packs', $username ) );
		return $response;
	}
}


class FreesoundAPI_Pack extends FreesoundAPI_Base
{
	public function Get( $id )
	{
		$response = $this->_request( $this->_requestUrl( 'pack', $id ) );
		return $response;
	}


	public function GetSounds( $id )
	{
		$response = $this->_request( $this->_requestUrl( 'pack_sounds', $id ) );
		return $response;
	}
}


class FreesoundAPI
{
	protected $_interfaces;

	public function __construct( $apiKey = null, $debug = false /*TODO*/ )
	{
		$this->_interfaces = new StdClass();
		$this->_interfaces->sound = new FreeSoundAPI_Sound( $apiKey );
		$this->_interfaces->user = new FreeSoundAPI_User( $apiKey );
		$this->_interfaces->pack = new FreeSoundAPI_Pack( $apiKey );
	}


	public function SetKey( $apiKey )
	{
		foreach( get_object_vars( $this->_interfaces ) as $var => $value ) {
			$this->_interfaces->{$var}->apiKey = $apiKey;
		}
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


//$fs = new FreesoundAPI();
//$fs->SetKey( '77bbf1a63bc84ccc9d80a38d6345ef60' );

$fs = new FreesoundAPI( '77bbf1a63bc84ccc9d80a38d6345ef60' );

$r = $fs->SoundSearch( 'foo' );
$r = $fs->Sound()->Search( 'foo' );
var_dump( $r );



//$fs->debug = true;
//$r = $fs->search( 'foo' );
//$r = $fs->sound( 120597 );
//$r = $fs->soundGeotag( 41.3265528618605, 41.4504467428547, 2.005176544189453, 2.334766387939453 );
//$r = $fs->soundAnalysis( 120597 );
//$r = $fs->soundAnalysis( 120597, array( 'lowlevel', 'tonal') );
//$r = $fs->soundSimilar( 120597 );
//$r = $fs->user( 'artshare' );
//$r = $fs->userSounds( 'artshare' );
//$r = $fs->userPacks( 'artshare' );
//$r = $fs->pack( 5107 );
//$r = $fs->packSounds( 5107 );

?>
