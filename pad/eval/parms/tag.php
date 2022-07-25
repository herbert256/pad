<?php

  if     ( pad_file_exists ( APP . "tags/$name.php"  ) ) return include 'tag_app.php';
  elseif ( pad_file_exists ( PAD . "tags/$name.php"  ) ) return include 'tag_pad.php';
  else                                                   return '';
  
?>