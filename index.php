<?php

	require_once 'lib/api.php';

	use Phpfastcache\Helper\Psr16Adapter;

	$defaultDriver = 'Files';
	$cache = new Psr16Adapter($defaultDriver);

	$art = '';
	$nowplaying = '';
	$current = current_track($cache);

	if (current_track_is_playing($current)) {
		$art = current_track_album_art($current);
		$nowplaying = 'Now Playing: ' . current_track_apa($current);
	} else {
		$nowplaying = current_track_apa($current);
	}

	$playlist = current_track_playlist($current, $cache);

	$desc = '';
	if (isset($playlist->name)) {
		$desc = 'Playlist: ' . $playlist->name;
	}

?>

<html prefix="og: http://ogp.me/ns#">
<head>
	<title><?php echo current_track_apa($current); ?></title>
	<meta property="og:title" content="<?php echo htmlspecialchars($nowplaying); ?>"/>
	<?php if (!empty($art)): ?>
		<meta property="og:image" content="<?php print $art; ?>"/>
	<?php endif; ?>
	<?php if (!empty($desc)): ?>
		<meta property="og:description" content="<?php print htmlspecialchars($desc); ?>"/>
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta name="twitter:title" content="<?php echo htmlspecialchars($nowplaying); ?>"/>
	<?php if (!empty($desc)): ?>
		<meta name="twitter:description" content="<?php print htmlspecialchars($desc); ?>"/>
	<?php endif; ?>
	<?php if (!empty($art)): ?>
		<meta name="twitter:image" content="<?php print $art; ?>"/>
	<?php endif; ?>

	<script src="https://cdn.jsdelivr.net/npm/vue"></script>

	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

	<style type="text/css">

		@import url("https://fonts.googleapis.com/css?family=Questrial");

		html {
			min-height: 100%;
			display: grid;
		}

		body {
			background: linear-gradient(to bottom left, #c0cfb2 10%, #8ba989 80%);
			display: grid;
			overflow: hidden;
		}

		#player {
			background: #ffffff;
			position: relative;
			margin: auto;
			width: 300px;
			height: 300px;
			overflow: hidden;
			border-radius: 5px;
			box-shadow: 5px 5px 15px rgba(54, 79, 60, 0.4);
			-webkit-transition: all .5s ease-in-out;
			transition: all .5s ease-in-out;
		}

		#player:hover {
			transform: scale(1.05);
		}

		.album {
			background-repeat: no-repeat;
			background-size: 300px;
			width: 100%;
			height: 100%;
			border-radius: 5px;
			position: absolute;
		}

		.heart {
			position: absolute;
			right: 0;
			color: #ffffff;
			margin: 10px;
			transition: all .4s ease;
		}

		.clicked {
			color: #49654d;
			transform: scale(1.2);
			transition: all .4s ease;
		}

		.info {
			height: 115px;
			width: 100%;
			position: absolute;
			bottom: 0;
			background: rgba(255, 255, 255, 0.85);
			transform: translateY(35px);
			transition: all .5s ease-in-out;
		}

		.up {
			transform: translateY(0px);
		}

		.progress-bar {
			height: 5px;
			width: 73%;
			margin: 4% auto;
			background: #cdd9c2;
			border-radius: 10px;
			font-family: "Questrial", sans-serif;
		}

		.fill {
			background-color: #8ba989;
			width: 0%;
			height: 0.3rem;
			border-radius: 2px;
		}

		.time--current, .time--total {
			color: #364f3c;
			font-size: 10px;
			position: absolute;
			margin-top: -2px;
		}

		.time--current {
			left: 15px;
		}

		.time--total {
			right: 15px;
		}

		.currently-playing {
			text-align: center;
			margin-top: -3px;
		}

		.song-name, .artist-name {
			font-family: "Questrial", sans-serif;
			text-transform: uppercase;
			margin: 0;
		}

		.song-name {
			font-size: .8em;
			letter-spacing: 3px;
			color: #364f3c;
		}

		.artist-name {
			font-size: .6em;
			letter-spacing: 1.5px;
			color: #557c5f;
			margin-top: 5px;
		}

		.controls {
			display: flex;
			align-items: center;
			font-size: .8em;
			justify-content: center;
			color: #8ba989;
		}

		.controls .play, .controls .pause {
			margin: 15px 25px;
			color: #6e946c;
		}

		.controls .option {
			left: 10px;
			position: absolute;
			font-size: .8em;
		}

		.controls .add {
			right: 10px;
			position: absolute;
			font-size: .8em;
		}

		.controls .volume {
			margin-right: 30px;
			font-size: .8em;
		}

		.controls .shuffle {
			margin-left: 30px;
			font-size: .8em;
		}

		.play, .pause, .next, .previous, .option, .add, .volume, .shuffle {
			transition: all .5s ease;
		}

		.play:hover, .pause:hover, .next:hover, .previous:hover, .option:hover, .add:hover, .volume:hover, .shuffle:hover {
			color: #557c5f;
		}

		footer {
			position: absolute;
			bottom: 0;
			right: 0;
			text-align: center;
			font-size: 0.5em;
			text-transform: uppercase;
			padding: 10px;
			color: #49654d;
			letter-spacing: 3px;
			font-family: "Questrial", sans-serif;
		}

		footer a {
			color: #ffffff;
			text-decoration: none;
		}

		footer a:hover {
			color: #49654d;
		}
	</style>

</head>
<body>
	<div id="player">
		<div class="album" v-bind:style="albumArt">
		</div>
		<div class="info" v-if="song.playing">
			<div class="progress-bar">
				<div class="time--current">{$ songPosition $}</div>
				<div class="time--total">{$ songLength $}</div>
				<div class="fill" v-bind:style="progressBar"></div>
			</div>
			<div class="currently-playing">
				<h2 class="song-name">{$ song.song $}</h2>
				<h3 class="artist-name">{$ song.artist $}</h3>
				<h4 class="artist-name">{$ song.albumname $}</h4>
			</div>
			<div class="controls">
				<div class="option"><i class="fas fa-bars"></i></div>
				<div class="volume"><i class="fas fa-volume-up"></i></div>
				<div class="previous"><i class="fas fa-backward"></i></div>
				<div class="play"><i class="fas fa-play"></i></div>
				<div class="pause"><i class="fas fa-pause"></i></div>
				<div class="next"><i class="fas fa-forward"></i></div>
				<div class="shuffle"><i class="fas fa-random"></i></div>
				<div class="add"><i class="fas fa-plus"></i></div>
			</div>
		</div>
	</div>
	<footer>
		<p>design made by <a href="https://codepen.io/juliepark"> julie</a> ♡
		<p>programmed to do stuff by ian moffitt</p>
	</footer>
	<script src="_files/js/main.js"></script>
</body>
</html>

