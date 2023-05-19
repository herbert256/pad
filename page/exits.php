<?php

  while ( $pad > $padRetrieveLevel ) 
    include pad . 'level/level.php'; 

  return $padHtml [$pad+1];

?>