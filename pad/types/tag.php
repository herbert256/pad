<?php

  if ( pad_file_exists ( PAD_APP  . "tags/$pad_tag.php"  ) )
    return include PAD_HOME . 'pad/types/app.php';
  else
    return include PAD_HOME . 'pad/types/pad.php';

?>