<?php

  if ( pad_file_exists ( PAD_APP  . "tags/$pad_tag.php"  ) )
    return include PAD_HOME . 'types/app.php';
  else
    return include PAD_HOME . 'types/pad.php';

?>