<?php

  if (!$pad_parm)
    $pad_parm = -1;

  $pad_idx = pad_idx ( $pad_parm ); 

  return include PAD_HOME . "tag/$name.php"; 

?>