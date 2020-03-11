<?php

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

	$api = _get_api_object();
	$me = $api->me();
	$devices = $api->getMyDevices();
	$playing = $api->getMyCurrentPlaybackInfo();
	$playing2 = $api->getMyCurrentTrack();

	$current = current_track($cache, $breaker);

	print_r($current);

	print_r($playing);

	print_r($playing2);

	print_r($me);

	print_r($devices);

