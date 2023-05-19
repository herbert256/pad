<?php

  if ( padTagParm ('open')  ) include 'open.php';

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('glue')  ) include 'glue.php';
  if ( padTagParm ('close') ) include 'close.php';

?>