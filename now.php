<?php

	const MAX_IMAGE_SIZE = 300;

	require_once 'lib/api.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$current = current_track($cache);

	if (current_track_is_playing($current)) {

		$art = current_track_album_art($current);

		if (!empty($art)) {
//			echo $art . PHP_EOL;
		}

		echo 'Now Playing: ' . current_track_apa($current) . PHP_EOL;

	} else {

		echo current_track_apa($current) . PHP_EOL;

	}

	$playlist = current_track_playlist($current, $cache);

	if (isset($playlist->name)) {
		echo 'Playlist: ' . $playlist->name;
	}
