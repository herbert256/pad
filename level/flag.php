<?php
  
  pad_trace ("level/end", "nr=$pad_lvl_cnt " . '@flag='.$pad_parms_tag ['flag'], TRUE);
  
  $pad_lvl--;
 
  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

?>