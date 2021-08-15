<?php

  if     ( pad_file_exists ( PAD_APP  . "tags/$name.php"  ) ) return include PAD_APP  . 'eval/tag_app.php';
  elseif ( pad_file_exists ( PAD_HOME . "pad/tags/$name.php"  ) ) return include PAD_HOME . 'pad/eval/tag_pad.php';
  else                                                        return '';
  
?>