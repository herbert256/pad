<?php

  if ( pad_file_exists ( APP . "tags/$pad_tag.php"  ) )
    return include PAD . 'types/tag_app.php';
  else
    return include PAD . 'types/tag_pad.php';

?>