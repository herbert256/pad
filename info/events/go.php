<?php

  include '/pad/info/events/result.php';

  if ( $GLOBALS ['padInfoTrace'] and str_contains ($padTagContent.$padContent, 'content@') )
   padInfoTrace ('constructs', 'content');  

  include '/pad/info/events/flags.php';

?>