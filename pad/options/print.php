<?php

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('quote') ) include 'options/quote.php';
  if ( padTagParm ('open')  ) include 'options/open.php';
  if ( padTagParm ('glue')  ) include 'options/glue.php';
  if ( padTagParm ('close') ) include 'options/close.php';

?>