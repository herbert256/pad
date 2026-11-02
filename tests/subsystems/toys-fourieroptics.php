<?php
$cmd='node '.escapeshellarg(__DIR__.'/toys-fourieroptics.cjs');passthru($cmd,$status);if($status)exit($status);
