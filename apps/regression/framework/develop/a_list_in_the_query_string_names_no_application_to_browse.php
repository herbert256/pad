<?php

  // A list from the query string - app[]=x, dir[]=x, file[]=x - names nothing: the apps
  // browser answers as it does for no name, where preg_match got the array and the page
  // ended on a TypeError, a 500.

  $answer = '';

  foreach ( [ 'app[]=x', 'app=demo&dir[]=x', 'app=demo&file[]=x' ] as $one )
    $answer .= padCurl ( $padHost . "apps/?browse&$one&padInclude" ) ['result'] . ' ';

?>
