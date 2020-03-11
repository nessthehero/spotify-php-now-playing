<?php

	print 'Spotify integration temporarily disabled';

	/*

	require_once 'lib/api.php';

	use Phpfastcache\Helper\Psr16Adapter;

	if (!isset($cache)) {
		$defaultDriver = 'Files';
		$cache = new Psr16Adapter($defaultDriver);
	}

	$breaker = false;
	if (!empty($skip_cache_breaker)) {
		$breaker = $skip_cache_breaker;
	}

	$current = current_track($cache, $breaker);
	$current_track = current_track_apa($current);

	$skipPassword = (!empty($_GET['p'])) ? $_GET['p'] : '';

	$skip = skip_track($skipPassword);

	if ($skip) {
		if (current_track_is_playing($current)) {
			echo '*Skipping:* ' . current_track_apa($current) . PHP_EOL;
		} else {
			echo '*Skipping* ' . PHP_EOL;
		}

		$skip_cache_breaker = TRUE;

		sleep(0.5);

		include 'now.php';

	} else {
		echo 'unable to skip' . PHP_EOL;
	}

/* */