<?php

  $padSeqInit = 'type';
  
  $padSeqInfo ['types'] [] = $padSeqType;

  if     ( $padSeqType == 'sequence' ) return include "sequence/inits/type/sequence.php";
  elseif ( $padSeqType == 'store'    ) return include "sequence/inits/type/store.php";
  elseif ( $padSeqType == 'action'   ) return include "sequence/inits/action.php";
  elseif ( $padSeqType == 'keep'     ) return include "sequence/inits/play.php";
  elseif ( $padSeqType == 'make'     ) return include "sequence/inits/play.php";
  elseif ( $padSeqType == 'remove'   ) return include "sequence/inits/play.php";
    
?>