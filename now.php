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

		$atoken = @file_get_contents('./access.txt');
		$rtoken = @file_get_contents('./refresh.txt');

		if (empty($atoken)) {
			echo 'Error: No valid token';
			die();
		} else {
			$api->setAccessToken($atoken);
		}

		$current = $api->getMyCurrentTrack();

		if (!empty($current)) {

			$song_title = $current->item->name;
			$song_artists = array();
			$song_album = $current->item->album->name;

			$artists = $current->item->artists;

			foreach ($artists as $key => $artist) {

				$song_artists[] = $artist->name;

			}

			// APA
			// Artist name, "Song title", Album

			$song_string = array();

			if (!empty($song_artists)) {

				$song_string[] = implode(', ', $song_artists);

			}

			if (!empty($song_title)) {

				$song_string[] = '"' . $song_title . '"';

			}

			if (!empty($song_album)) {

				$song_string[] = $song_album;

			}

			if ($current->is_playing) {
				echo implode(', ', $song_string);
			} else {
				echo 'Nothing is currently playing. Last song played: ' . implode(', ', $song_string);
			}

		} else {
			echo 'Nothing is currently playing.';
		}

	} else {
		echo 'Error: Bad client id/secret';
	}