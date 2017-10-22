<?php

if (!is_dir($cache_dir)) { // create the cache directory if it doesn't exist
	mkdir($cache_dir);
}

$time_taken = round(microtime(TRUE) - $start_time, 5);
echo "<!-- Dynamic page generated in $time_taken seconds. -->\n";
echo "<!-- Cached page generated on " . date('Y-m-d H:i:s') . " -->\n\n";

// write the file
if($cache) {
	$fp = fopen($cache_file, 'w');
	fwrite($fp, ob_get_contents());
	fclose($fp);
}

ob_end_flush();
