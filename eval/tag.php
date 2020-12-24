<?php

  if ( file_exists ( PAD_APP  . "tags/$name.php"  ) )
    return include PAD_HOME . 'eval/tag_app.php';
  else
    return include PAD_HOME . 'eval/tag_pad.php';

?>