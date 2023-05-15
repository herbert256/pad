<?php
 
include pad . 'page/start.php';
include pad . 'build/build.php';    

$pad--;

include pad . 'page/end.php'; 

return $padBase [$pad+1];

?>