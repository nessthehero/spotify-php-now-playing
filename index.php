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

	$seconds_left = current_track_seconds_left($current);

/*?>

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
	<?php if (!empty($seconds_left)): ?>
		<meta http-equiv="refresh" content="<?php print $seconds_left; ?>" />
	<?php endif; ?>
</head>
<?php */

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

/*?>

</html>*/

