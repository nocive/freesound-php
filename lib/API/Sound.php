<?php

/**
 * Sound class
 *
 * @package    Freesound
 * @subpackage Freesound_API
 */
class Freesound_API_Sound extends Freesound_API_Base
{
	const URL_SOUND = '/sounds/<sound_id>/';
	const URL_SOUND_ANALYSIS = '/sounds/<sound_id>/analysis/<filter>/';
	const URL_SOUND_ANALYSIS_NO_FILTER = '/sounds/<sound_id>/analysis/';
	const URL_SOUND_GEOTAG = '/sounds/geotag/';
	const URL_SOUND_SIMILAR = '/sounds/<sound_id>/similar/';
	const URL_SOUND_SEARCH = '/sounds/search/';


	public function Get( $id = null )
	{
		$params = $this->_PrepareParams( compact( 'id' ) );
		return $this->_Request( 'sound', $params['id'] );
	}


	public function GetAnalysis( $id = null, $filter = null, $all = false )
	{
		$params = $this->_PrepareParams( compact( 'id', 'filter', 'all' ) );

		if (empty( $params['filter'] )) {
			$params['filter'] = false;
			$method = 'sound_analysis_no_filter';
		} else {
			$method = 'sound_analysis';
			if (is_array( $params['filter'] )) {
				$params['filter'] = implode( '/', $params['filter'] );
			}
		}

		return $this->_Request( $method, array( $params['id'], $params['filter'] ), array(
			'all' => $params['all']
		) );
	}


	public function GetSimilar( $id = null, $num = null, $preset = null, $fields = null )
	{
		$params = $this->_PrepareParams( compact( 'id', 'num', 'preset', 'fields' ) );

		return $this->_Request( 'sound_similar', $params['id'], array(
			'num_results' => $params['num'],
			'preset' => $params['preset'],
			'fields' => $params['fields']
		) );
	}


	public function Search( $query, $page = null, $filter = null, $sort = null, $fields = null )
	{
		$params = $this->_PrepareParams( compact( 'query', 'page', 'filter', 'sort', 'fields' ) );

		return $this->_Request( 'sound_search', null, array(
			'q' => $params['query'],
			'p' => $params['page'],
			'f' => $params['filter'],
			's' => $params['sort'],
			'fields' => $params['fields']
		) );
	}


	public function SearchGeo( $minLat = null, $maxLat = null, $minLon = null, $maxLon = null, $page = null, $fields = null )
	{
		$params = $this->_PrepareParams( compact( 'minLat', 'maxLat', 'minLon', 'maxLon', 'page', 'fields' ) );

		return $this->_Request( 'sound_geotag', null, array(
			'min_lat' => $params['minLat'],
			'max_lat' => $params['maxLat'],
			'min_lon' => $params['minLon'],
			'max_lon' => $params['maxLon'],
			'p' => $params['page'],
			'fields' => $params['fields']
		) );
	}
}

?>
