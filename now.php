<?php

	require_once 'lib/api.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$breaker = false;
	if (!empty($skip_cache_breaker)) {
		$breaker = $skip_cache_breaker;
	}

	$current = current_track($cache, $breaker);

	if (current_track_is_playing($current)) {

		$art = current_track_album_art($current);

		if (!empty($art)) {
//			echo $art . PHP_EOL;
		}

		echo '*Now Playing:* ' . current_track_apa($current) . PHP_EOL;

	} else {

		echo current_track_apa($current) . PHP_EOL;

	}

	$playlist = current_track_playlist($current, $cache);

	if (isset($playlist->name)) {
		echo '*Playlist:* ' . $playlist->name;
	}
