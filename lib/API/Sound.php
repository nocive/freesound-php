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
		$id = $this->_id( $id );
		return $this->_Request( 'sound', $id );
	}


	public function GetAnalysis( $id = null, $filter = null, $all = false )
	{
		$id = $this->_id( $id );

		if (empty( $filter )) {
			$filter = false;
			$method = 'sound_analysis_no_filter';
		} else {
			$method = 'sound_analysis';
			if (is_array( $filter )) {
				$filter = implode( '/', $filter );
			}
		}

		return $this->_Request( $method, array( $id, $filter ), array(
			'all' => $all
		));
	}


	public function GetSimilar( $id = null, $num = null, $preset = null, $fields = null )
	{
		$id = $this->_id( $id );

		return $this->_Request( 'sound_similar', $id, array(
			'num_results' => $num,
			'preset' => $preset,
			'fields' => $fields
		));
	}


	public function Search( $query, $page = null, $filter = null, $sort = null, $fields = null )
	{
		return $this->_Request( 'sound_search', null, array(
			'q' => $query,
			'p' => $page,
			'f' => $filter,
			's' => $sort,
			'fields' => $fields
		));
	}


	public function SearchGeo( $minLat = null, $maxLat = null, $minLon = null, $maxLon = null, $page = null, $fields = null )
	{
		return $this->_Request( 'sound_geotag', null, array(
			'min_lat' => $minLat,
			'max_lat' => $maxLat,
			'min_lon' => $minLon,
			'max_lon' => $maxLon,
			'p' => $page,
			'fields' => $fields
		));
	}
}

?>
