<?php

  if ( $GLOBALS ['padInfo'] ) include '/pad/info/events/result.php';

  if ( $GLOBALS ['padInfo'] and str_contains ($padTagContent.$padContent, 'content@') )
   padTrace ('constructs', 'content');  

  if ( $GLOBALS ['padInfo'] ) include '/pad/info/events/flags.php';

?>