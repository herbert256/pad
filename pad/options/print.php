<?php

  $padContent .= '{&firstFieldValue}';

  if ( padTagParm ('quote') ) include PAD . 'options/quote.php';
  if ( padTagParm ('open')  ) include PAD . 'options/open.php';
  if ( padTagParm ('glue')  ) include PAD . 'options/glue.php';
  if ( padTagParm ('close') ) include PAD . 'options/close.php';

?>
