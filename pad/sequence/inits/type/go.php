<?php

  $padStartType = 'type';
  
  $padSeqInfo ['types'] [] = $padType [$pad];

  if     ( $padType [$pad] == 'sequence' ) return include "sequence/inits/type/sequence.php";
  elseif ( $padType [$pad] == 'store'    ) return include "sequence/inits/type/store.php";
  elseif ( $padType [$pad] == 'action'   ) return include "sequence/inits/type/action.php";
  elseif ( $padType [$pad] == 'keep'     ) return include "sequence/inits/type/play.php";
  elseif ( $padType [$pad] == 'make'     ) return include "sequence/inits/type/play.php";
  elseif ( $padType [$pad] == 'remove'   ) return include "sequence/inits/type/play.php";
    
?>