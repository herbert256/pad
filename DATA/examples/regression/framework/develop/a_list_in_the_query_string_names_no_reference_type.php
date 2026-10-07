<?php

  // A list from the query string where the reference pages take the name of a type -
  // type[]=x - is no type: they show the default one, where the title made of it ended the
  // page on "Array to string conversion", a 500. xref[] and item[] were already no name.

  $answer = '';

  foreach ( [ 'dir&type[]=x', 'pages&type[]=x' ] as $one )
    $answer .= padCurl ( $padHost . "reference/?$one&padInclude" ) ['result'] . ' ';

?>
