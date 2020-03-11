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
	$recent = $api->getMyRecentTracks();

	$current = current_track($cache, $breaker);

	print_r('-- current --' . PHP_EOL);
	print_r($current);
	print_r('-- end current --' . PHP_EOL);

	print_r('-- playing --' . PHP_EOL);
	print_r($playing);
	print_r('-- end playing --' . PHP_EOL);

	print_r('-- playing2 --' . PHP_EOL);
	print_r($playing2);
	print_r('-- end playing2 --' . PHP_EOL);

	print_r('-- recent --' . PHP_EOL);
	print_r($recent);
	print_r('-- end recent --' . PHP_EOL);

	print_r('-- me --' . PHP_EOL);
	print_r($me);
	print_r('-- end me --' . PHP_EOL);

	print_r('-- devices --' . PHP_EOL);
	print_r($devices);
	print_r('-- end devices --' . PHP_EOL);

