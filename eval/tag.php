<?php

  if     ( file_exists ( PAD_APP  . "tags/$name.php"  ) ) return include PAD_APP  . 'eval/tag_app.php';
  elseif ( file_exists ( PAD_HOME . "tags/$name.php"  ) ) return include PAD_HOME . 'eval/tag_pad.php';
  else                                                    return $name;
  
?>