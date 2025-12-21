<?php

	// Read JSON file from _data/
	$jsonContent = file_get_contents(APP . "_data/$padParm.json");

	// Compact JSON by removing whitespace (decode then re-encode without pretty print)
	$jsonData = json_decode($jsonContent, true);
	$jsonCompact = json_encode($jsonData);

	// HTML-escape for use in attributes
	$padContent = htmlspecialchars($jsonCompact, ENT_QUOTES, 'UTF-8');

	return TRUE;

?>