<?php

  if ( pad_file_exists ( APP . "tags/$pad_tag.php" ) or pad_file_exists ( APP . "tags/$pad_tag.html" ) )
    return include 'tag_app.php';
  else
    return include 'tag_pad.php';

?>