<?php

      if ( $padType [$pad] == 'action'   ) return include "seq/inits/type/operation.php";
  elseif ( $padType [$pad] == 'keep'     ) return include "seq/inits/type/play.php";
  elseif ( $padType [$pad] == 'make'     ) return include "seq/inits/type/play.php";
  elseif ( $padType [$pad] == 'one'      ) return include "seq/inits/type/operation.php";
  elseif ( $padType [$pad] == 'remove'   ) return include "seq/inits/type/play.php";
  elseif ( $padType [$pad] == 'sequence' ) return include "seq/inits/type/sequence.php";
  elseif ( $padType [$pad] == 'store'    ) return include "seq/inits/type/store.php";
    
?>