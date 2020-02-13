<?php

	require 'vendor/autoload.php';

	$dotenv = Dotenv\Dotenv::create(__DIR__);
	$dotenv->load();

	$SPOTIFY_CLIENT_ID = getenv('SPOTIFY_CLIENT_ID');
	$SPOTIFY_CLIENT_SECRET = getenv('SPOTIFY_CLIENT_SECRET');
	$SPOTIFY_REDIRECT_URI = getenv('SPOTIFY_REDIRECT_URI');
	$SKIP_PASSWORD = getenv('SKIP_PASSWORD');
	$SALT = getenv('SALT');

	if (empty($SALT)) {
		$SALT = '_';
	} else {
		$SALT .= '_';
	}

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

				$writeAccess = @file_put_contents('./' . $SALT . 'access.txt', $accessToken);
				$writeRefresh = @file_put_contents('./' . $SALT . 'refresh.txt', $refreshToken);

				header('Location: /index.php');
			} catch (Exception $e) {
				echo 'Spotify API Error: ' . $e->getCode();
				die();
			}

		}

		$atoken = @file_get_contents('./' . $SALT . 'access.txt');
		$rtoken = @file_get_contents('./' . $SALT . 'refresh.txt');

		if (empty($atoken)) {
			unlink('./' . $SALT . 'access.txt');
			unlink('./' . $SALT . 'refresh.txt');

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

		$skipPassword = (!empty($_GET['p'])) ? $_GET['p'] : '';

		try {
			if (empty($SKIP_PASSWORD) || empty($skipPassword) || ($SKIP_PASSWORD !== $skipPassword)) {
				throw new Exception('password');
			} else {
				$api->next(0);
				print 'skipping...';
			}
		} catch (Exception $e) {
			if ($e->getMessage() === 'password') {
				echo "I'm sorry, I can't do that. (BAD PASSWORD)";
			} else {
				try {
					$session->refreshAccessToken($rtoken);
					$accessToken = $session->getAccessToken();
					$api->setAccessToken($accessToken);

					unlink('./' . $SALT . 'access.txt');
					$writeAccess = @file_put_contents('./' . $SALT . 'access.txt', $accessToken);

					$api->next(0);
					print 'skipping...';
				} catch (Exception $e) {
					echo 'Error: ' . $e->getMessage() . ' (1)';
				}
			}
		}

	} else {
		echo 'Error: Bad client id/secret (1)';
	}