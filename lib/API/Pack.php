<?php

/**
 * Pack class
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class Freesound_API_Pack extends Freesound_API_Base
{
	const URL_PACK = '/packs/<pack_id>/';
	const URL_PACK_SOUNDS = '/packs/<pack_id>/sounds/';


	public function Get( $id = null )
	{
		$id = $this->_id( $id );
		return $this->_Request( 'pack', $id );
	}


	public function GetSounds( $id = null )
	{
		$id = $this->_id( $id );
		return $this->_Request( 'pack_sounds', $id );
	}
}

?>
