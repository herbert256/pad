<?php

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('quote') ) include '/pad/options/quote.php';
  if ( padTagParm ('open')  ) include '/pad/options/open.php';
  if ( padTagParm ('glue')  ) include '/pad/options/glue.php';
  if ( padTagParm ('close') ) include '/pad/options/close.php';

?>