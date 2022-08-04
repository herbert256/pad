<?php

  if     ( file_exists ( APP . "tags/$name.php"  ) ) return include 'tag_app.php';
  elseif ( file_exists ( PAD . "tags/$name.php"  ) ) return include 'tag_pad.php';
  else                                               return '';
  
?>