<?php
$cmd='node '.escapeshellarg(__DIR__.'/toys-voronoi.cjs');passthru($cmd,$status);if($status)exit($status);
