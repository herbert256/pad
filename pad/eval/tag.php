<?php

  if     ( pad_file_exists ( APP  . "tags/$name.php"  ) ) return include APP  . 'eval/tag_app.php';
  elseif ( pad_file_exists ( PAD . "tags/$name.php"  ) ) return include PAD . 'eval/tag_pad.php';
  else                                                        return '';
  
?>