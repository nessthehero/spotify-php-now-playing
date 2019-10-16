<?php

	require 'vendor/autoload.php';

	$dotenv = Dotenv\Dotenv::create(__DIR__);
	$dotenv->load();

	$SPOTIFY_CLIENT_ID = getenv('SPOTIFY_CLIENT_ID');
	$SPOTIFY_CLIENT_SECRET = getenv('SPOTIFY_CLIENT_ID');
	$SPOTIFY_REDIRECT_URI = getenv('SPOTIFY_CLIENT_ID');

	if (!empty($SPOTIFY_CLIENT_ID) && !empty($SPOTIFY_CLIENT_SECRET) && !empty($SPOTIFY_REDIRECT_URI)) {

		$session = new SpotifyWebAPI\Session(
			$SPOTIFY_CLIENT_ID,
			$SPOTIFY_CLIENT_SECRET,
			$SPOTIFY_REDIRECT_URI
		);

		$api = new SpotifyWebAPI\SpotifyWebAPI();

		if (isset($_GET['code'])) {
			try {
				$session->requestAccessToken($_GET['code']);
				$api->setAccessToken($session->getAccessToken());

				$accessToken = $session->getAccessToken();
				$refreshToken = $session->getRefreshToken();

				$writeAccess = @file_put_contents('./access.txt', $accessToken);
				$writeRefresh = @file_put_contents('./refresh.txt', $refreshToken);

				header('Location: /index.php');
			} catch (Exception $e) {
				echo 'Spotify API Error: ' . $e->getCode();
			}
			die();
		}

		$atoken = @file_get_contents('./access.txt');
		$rtoken = @file_get_contents('./refresh.txt');

		if (empty($atoken)) {
			unlink('./access.txt');
			unlink('./refresh.txt');

			$options = [
				'scope' => [
					'user-read-currently-playing',
					'user-read-playback-state'
				],
			];

			header('Location: ' . $session->getAuthorizeUrl($options));
			die();
		} else {
			$api->setAccessToken($atoken);
		}

		print_r($api->me());

	} else {
		echo 'Error: Bad client id/secret';
	}