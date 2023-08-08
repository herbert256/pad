<?php

  $padContent .= '{$' . $padName [$pad] . '}';

  if ( padTagParm ('quote') ) include pad . '_options/quote.php';
  if ( padTagParm ('open')  ) include pad . '_options/open.php';
  if ( padTagParm ('glue')  ) include pad . '_options/glue.php';
  if ( padTagParm ('close') ) include pad . '_options/close.php';

?>