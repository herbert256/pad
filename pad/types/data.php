<?php

  if ( pad_tag_parm ('print') )
    include PAD_HOME . 'pad/options/print.php';

  return $pad_data_store [$pad_tag];
 
?>