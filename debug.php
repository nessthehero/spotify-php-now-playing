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

	$current = current_track($cache, $breaker);

	print_r('-- current --' . PHP_EOL);
	print_r($current);
	print_r($api->getLastResponse());
	print_r('-- end current --' . PHP_EOL);

	$playing = $api->getMyCurrentPlaybackInfo();

	print_r('-- playing --' . PHP_EOL);
	print_r($playing);
	print_r($api->getLastResponse());
	print_r('-- end playing --' . PHP_EOL);

	$playing2 = $api->getMyCurrentTrack();

	print_r('-- playing2 --' . PHP_EOL);
	print_r($playing2);
	print_r($api->getLastResponse());
	print_r('-- end playing2 --' . PHP_EOL);

	$recent = $api->getMyRecentTracks();

	print_r('-- recent --' . PHP_EOL);
	print_r($recent);
	print_r('-- end recent --' . PHP_EOL);

	$me = $api->me();

	print_r('-- me --' . PHP_EOL);
	print_r($me);
	print_r('-- end me --' . PHP_EOL);

	$devices = $api->getMyDevices();

	print_r('-- devices --' . PHP_EOL);
	print_r($devices);
	print_r('-- end devices --' . PHP_EOL);

	if (!empty($_GET['device'])) {

		$deviceId = $_GET['device'];

		try {
			$device = $api->play($deviceId);
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
		print_r($api->getLastResponse());

	}