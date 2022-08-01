<?php

  if ( file_exists ( APP . "tags/$pad_tag.php" ) or file_exists ( APP . "tags/$pad_tag.html" ) )
    return include 'tag_app.php';
  else
    return include 'tag_pad.php';

?>