<?php

  while ( $pad > $padRetrieveLevel ) 
    include PAD . 'level/level.php'; 

  return $padHtml [$pad+1];

?>