<?php

/**
 * User class
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class Freesound_API_User extends Freesound_API_Base
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

?>
