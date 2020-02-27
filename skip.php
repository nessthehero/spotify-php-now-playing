<?php

	require_once 'lib/api.php';

	$skipPassword = (!empty($_GET['p'])) ? $_GET['p'] : '';

	$skip = skip_track($skipPassword);

	if ($skip) {
		echo 'skipping...' . PHP_EOL;

		include 'now.php';

	} else {
		echo 'unable to skip' . PHP_EOL;
	}

