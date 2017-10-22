<?php

$start_time = microtime(TRUE);

$cache = false;
$cache_time = 300; // 5 minutes
$gc_time = 120; // 2 minutes
$cache_dir = 'cache/';

$cache_file = $cache_dir . md5($url) . '.html';
$gc_time_file = $cache_dir . 'last_gc_run';

// garbage collection
if (!file_exists($gc_time_file)) {
	touch($gc_time_file);
}
if (time() - $gc_time > filemtime($gc_time_file)) {
	$expiryTime = time() - $cache_time;
	$cachedFiles = array_diff(scandir($cache_dir), array('.', '..'));

	foreach($cachedFiles as $file) {
		$path = $cache_dir . $file;
		if($expiryTime > filemtime($path)) {
			unlink($path);
		}
	}
	touch($gc_time_file);
}

if (file_exists($cache_file)) {
	ob_start();
	readfile($cache_file);

	$time_taken = round(microtime(TRUE) - $start_time, 5);
	echo "<!-- Cached page retrieved in $time_taken seconds. -->";

	ob_end_flush();
	die();
}

ob_start();
