<?php

  $padSeqInfo ['types'] [] = $padType [$pad];

      if ( $padType [$pad] == 'action'   ) return include "sequence/inits/type/operation.php";
  elseif ( $padType [$pad] == 'keep'     ) return include "sequence/inits/type/play.php";
  elseif ( $padType [$pad] == 'make'     ) return include "sequence/inits/type/play.php";
  elseif ( $padType [$pad] == 'one'      ) return include "sequence/inits/type/operation.php";
  elseif ( $padType [$pad] == 'remove'   ) return include "sequence/inits/type/play.php";
  elseif ( $padType [$pad] == 'sequence' ) return include "sequence/inits/type/sequence.php";
  elseif ( $padType [$pad] == 'store'    ) return include "sequence/inits/type/store.php";
    
?>