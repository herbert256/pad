<?php

  while ( $pad > $padRetrieveLevel ) 
    include 'level/level.php'; 

  return $padHtml [$pad+1];

?>