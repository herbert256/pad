<?php

  if ( padTagParm ('open')  ) include pad . '_options/open.php';

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('glue')  ) include pad . '_options/glue.php';
  if ( padTagParm ('close') ) include pad . '_options/close.php';

?>