<?php

  if ( padTagParm ('open')  ) include '_options/open.php';

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('glue')  ) include '_options/glue.php';
  if ( padTagParm ('close') ) include '_options/close.php';

?>