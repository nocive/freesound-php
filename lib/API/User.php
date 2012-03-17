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


	public function Get( $username = null )
	{
		$username = $this->_id( $username );
		return $this->_Request( 'user', $username );
	}


	public function GetSounds( $username = null )
	{
		$username = $this->_id( $username );
		return $this->_Request( 'user_sounds', $username );
	}


	public function GetPacks( $username = null )
	{
		$username = $this->_id( $username );
		return $this->_Request( 'user_packs', $username );
	}
}

?>
