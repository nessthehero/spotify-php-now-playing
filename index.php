<?php

	const MAX_IMAGE_SIZE = 300;

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

	$desc = $nowplaying;
	if (isset($playlist->name)) {
		$desc .= PHP_EOL . $playlist->name;
	}

?>

<html prefix="og: http://ogp.me/ns#">
	<head>
		<title><?php echo current_track_apa($current); ?></title>
		<meta property="og:title" content="<?php echo htmlspecialchars($nowplaying); ?>" />
		<?php if (!empty($art)): ?>
		<meta property="og:image" content="<?php print $art; ?>"/>
		<?php endif; ?>
		<meta property="og:description" content="<?php print htmlspecialchars($desc); ?>"/>
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:title" content="<?php echo htmlspecialchars($nowplaying); ?>"/>
		<meta name="twitter:description" content="<?php print htmlspecialchars($desc); ?>"/>
		<?php if (!empty($art)): ?>
		<meta name="twitter:image" content="<?php print $art; ?>"/>
		<?php endif; ?>
	</head>
<?php

	if (current_track_is_playing($current)) {

		if (!empty($art)) {
			echo $art . PHP_EOL;
		}

		echo 'Now Playing: ' . current_track_apa($current) . PHP_EOL;

	} else {

		echo current_track_apa($current) . PHP_EOL;

	}

	if (isset($playlist->name)) {
		echo 'Playlist: ' . $playlist->name;
	}

?>

</html>
