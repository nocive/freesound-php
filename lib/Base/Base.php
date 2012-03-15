<?php

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
}

?>
