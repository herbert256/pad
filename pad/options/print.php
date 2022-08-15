<?php

  if ( padTagParm ('open')  ) include PAD . 'options/open.php';

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('glue')  ) include PAD . 'options/glue.php';
  if ( padTagParm ('close') ) include PAD . 'options/close.php';

?>