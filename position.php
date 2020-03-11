<?php

	/*

	$response = array(
		'playing' => false,
		'song' => '',
		'artist' => '',
		'albumname' => '',
		'progress' => array(
			'progress' => 0,
			'length' => 0,
			'remaining' => 0
		),
		'albumart' => '',
		'playlist' => '',
		'href' => ''
	);

	print json_encode($response);

	/* */

	require_once 'lib/api.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$current = current_track($cache);

	if (current_track_is_playing($current)) {

		$response['playing'] = true;

		$art = current_track_album_art($current);

		if (!empty($art)) {
			$response['albumart'] = $art;
		}

		$response['song'] = current_track_song_title($current);
		$response['artist'] = current_track_artists($current);
		$response['albumname'] = current_track_album($current);

		$response['progress'] = current_track_progress($current);
		$response['href'] = current_track_href($current);

	}

	$playlist = current_track_playlist($current, $cache);

	if (isset($playlist->name)) {
		$response['playlist'] = $playlist->name;
	} else {
		$response['playlist'] = '----';
	}

	print json_encode($response);

	/* */