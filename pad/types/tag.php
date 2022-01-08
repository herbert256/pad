<?php

  if ( pad_file_exists ( APP  . "tags/$pad_tag.php"  ) )
    return include PAD . 'types/app.php';
  else
    return include PAD . 'types/pad.php';

?>