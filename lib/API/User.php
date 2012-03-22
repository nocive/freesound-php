<?php

/**
 * User class
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class Freesound_API_User extends Freesound_API_Base
{
	const URL_USER = '/people/<username>/';
	const URL_USER_SOUNDS = '/people/<username>/sounds/';
	const URL_USER_PACKS = '/people/<username>/packs/';
	const URL_USER_BOOKMARK_CATEGORIES = '/people/<username>/bookmark_categories/';
	const URL_USER_BOOKMARK_CATEGORY_SOUNDS = '/people/<username>/bookmark_categories/<category_id>/sounds/';


	public function Get( $username = null )
	{
		$params = $this->_PrepareParams( compact( 'username' ) );
		return $this->_Request( 'user', $params['username'] );
	}


	public function GetSounds( $username = null )
	{
		$params = $this->_PrepareParams( compact( 'username' ) );
		return $this->_Request( 'user_sounds', $params['username'] );
	}


	public function GetPacks( $username = null )
	{
		$params = $this->_PrepareParams( compact( 'username' ) );
		return $this->_Request( 'user_packs', $params['username'] );
	}


	public function GetBookmarkCategories( $username = null )
	{
		$params = $this->_PrepareParams( compact( 'username' ) );
		return $this->_Request( 'user_bookmark_categories', $params['username'] );
	}


	public function GetBookmarkCategorySounds( $username = null, $categoryId = null, $page = null, $fields = null, $soundsPerPage = null )
	{
		$params = $this->_PrepareParams( compact( 'username', 'categoryId', 'page', 'fields', 'soundsPerPage' ) );
		$params['categoryId'] = empty( $params['categoryId'] ) ? 'uncategorized' : $params['categoryId'];

		return $this->_Request( 'user_bookmark_category_sounds', array( $params['username'], $params['categoryId'] ), array(
			'p' => $params['page'],
			'fields' => $params['fields'],
			'sounds_per_page' => $params['soundsPerPage']
		) );
	}
}

?>
