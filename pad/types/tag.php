<?php

  if ( file_exists ( APP . "tags/$pTag.php" ) or file_exists ( APP . "tags/$pTag.html" ) )
    return include 'tag_app.php';
  else
    return include 'tag_pad.php';

?>