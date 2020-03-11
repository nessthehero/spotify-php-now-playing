<?php

	const MAX_IMAGE_SIZE = 300;
	const ROOT_DIR = __DIR__ . '/../';

	require ROOT_DIR . 'vendor/autoload.php';

	function _get_api_object()
	{

		$dotenv = Dotenv\Dotenv::create(ROOT_DIR);
		$dotenv->load();

		$SPOTIFY_CLIENT_ID = getenv('SPOTIFY_CLIENT_ID');
		$SPOTIFY_CLIENT_SECRET = getenv('SPOTIFY_CLIENT_SECRET');
		$SPOTIFY_REDIRECT_URI = getenv('SPOTIFY_REDIRECT_URI');
		$SALT = getenv('SALT');

		if (empty($SALT)) {
			$SALT = '_';
		} else {
			$SALT .= '_';
		}

		$api = null;

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

					$writeAccess = @file_put_contents(ROOT_DIR . $SALT . 'access.txt', $accessToken);
					$writeRefresh = @file_put_contents(ROOT_DIR . $SALT . 'refresh.txt', $refreshToken);

					header('Location: /index.php');
				} catch (Exception $e) {
					echo 'Spotify API Error: ' . $e->getCode();
					die();
				}

			}

			$atoken = @file_get_contents(ROOT_DIR . $SALT . 'access.txt');
			$rtoken = @file_get_contents(ROOT_DIR . $SALT . 'refresh.txt');

			if (empty($atoken)) {
				unlink(ROOT_DIR . $SALT . 'access.txt');
				unlink(ROOT_DIR . $SALT . 'refresh.txt');

				$options = [
					'scope' => [
						'user-read-currently-playing',
						'user-read-recently-played',
						'user-read-playback-state',
						'user-modify-playback-state'
					],
				];

				header('Location: ' . $session->getAuthorizeUrl($options));
				die();
			} else {
				$api->setAccessToken($atoken);
			}

			$me = null;

			try {
				$me = $api->me();
			} catch (Exception $e) {
				try {
					$session->refreshAccessToken($rtoken);
					$accessToken = $session->getAccessToken();
					$api->setAccessToken($accessToken);

					unlink(ROOT_DIR . $SALT . 'access.txt');
					$writeAccess = @file_put_contents(ROOT_DIR . $SALT . 'access.txt', $accessToken);

					$me = $api->me();
				} catch (Exception $e) {
					echo 'Error: Bad session token or client (1)';
					die();
				}
			}

		} else {
			echo 'Error: Bad client id/secret (1)';
			die();
		}

		if (empty($me)) {
			echo 'Error: me() object is empty (1)';
			die();
		}

		return $api;

	}

	function current_track($cache, $breaker = false) {

		$current = null;
		$api = _get_api_object();

		if ($cache->has('current_playing') && !$breaker) {
			$current = $cache->get('current_playing');
		} else {
			try {
				$current = $api->getMyCurrentTrack();
			} catch (Exception $e) {
				echo 'Error: ' . $e->getMessage();
				die();
			}

			$cache->set('current_playing', $current, 5);
		}

		return $current;

	}

	function current_track_is_playing($current) {

		if (isset($current->is_playing)) {
			return $current->is_playing;
		} else {
			return FALSE;
		}

	}

	function current_track_album_art($current) {

		$art = '';

		if (isset($current->item->album->images)) {
			$album_art = $current->item->album->images;

			while($art === '' && !empty($album_art)) {

				$item = array_shift($album_art);

				if ($item->height <= MAX_IMAGE_SIZE) {

					$art = $item->url;

				}

			}

		}

		return $art;

	}

	function current_track_playlist($current, $cache) {

		$playlist = '';

		if (isset($current->context->uri)) {

			if ($cache->has('current_playlist')) {
				$playlist = $cache->get('current_playlist');
			} else {

				$api = _get_api_object();

				$playlistId = $current->context->uri;

				try {

					$playlist = $api->getPlaylist($playlistId);

					$cache->set('current_playlist', $playlist, 5);

				} catch (Exception $e) {

					$cache->set('current_playlist', $playlist, 5);

					// Mute errors on this

				}

			}

		}

		return $playlist;

	}

	function current_track_progress($current) {

		$progress = 0;
		$length = 0;

		if (isset($current->progress_ms)) {

			$progress = $current->progress_ms;

		}

		if (isset($current->item->duration_ms)) {

			$length = $current->item->duration_ms;

		}

		if (!current_track_is_playing($current)) {
			$progress = 0;
			$length = 0;
		}

		return array(
			'progress' => $progress,
			'length' => $length,
			'remaining' => $length - $progress
		);

	}

	function current_track_seconds_left($current) {

		$progress = current_track_progress($current);

		$remaining = 0;

		if (!empty($progress['remaining'])) {

			$remaining = ceil($progress['remaining'] / 1000);

		}

		return $remaining;

	}

	function current_track_href($current) {

		if (isset($current->item->external_urls->spotify)) {
			return $current->item->external_urls->spotify;
		}

		return '';

	}

	function current_track_apa($current) {

		$return = '';

		if (!empty($current)) {

			$song_title = current_track_song_title($current);
			$song_artists = current_track_artists($current);
			$song_album = current_track_album($current);

			// APA
			// Artist name, "Song title", Album

			$song_string = array();

			if (!empty($song_artists)) {

				$song_string[] = $song_artists;

			}

			if (!empty($song_title)) {

				$song_string[] = '"' . $song_title . '"';

			}

			if (!empty($song_album)) {

				$song_string[] = $song_album;

			}

			if ($current->is_playing) {
				$return = implode(', ', $song_string);
			} else {
				$return = 'Nothing is currently playing. ' . PHP_EOL . '*Last song played:* ' . implode(', ', $song_string);
			}

		} else {
			$return = 'Nothing is currently playing.';
		}

		return $return;

	}

	function current_track_song_title($current) {

		$song_title = '';

		if (!empty($current)) {

			$song_title = $current->item->name;

		}

		return $song_title;

	}

	function current_track_artists($current) {

		$song_artists = array();

		$artists = $current->item->artists;

		foreach ($artists as $key => $artist) {

			$song_artists[] = $artist->name;

		}

		return implode(', ', $song_artists);

	}

	function current_track_album($current) {

		$song_album = '';

		if (!empty($current)) {

			$song_album = $current->item->album->name;

		}

		return $song_album;

	}

	function skip_track($password) {

		$dotenv = Dotenv\Dotenv::create(ROOT_DIR);
		$dotenv->load();

		$SKIP_PASSWORD = getenv('SKIP_PASSWORD');

		$api = _get_api_object();

		$skipped = FALSE;

		try {
			if (empty($SKIP_PASSWORD) || empty($password) || ($SKIP_PASSWORD !== $password)) {
				throw new Exception('password');
			} else {
				$skipped = $api->next(0);
			}
		} catch (Exception $e) {
			if ($e->getMessage() === 'password') {
				echo "I'm sorry, I can't do that. (BAD PASSWORD)";
				die();
			} else {
				echo 'Error: ' . $e->getMessage() . ' (1)';
				die();
			}
		}

		return $skipped;

	}
