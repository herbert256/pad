<?php

      if ( $padTag  [$pad] == 'action'   ) return include "sequence/inits/tag/go.php";
  elseif ( $padTag  [$pad] == 'keep'     ) return include "sequence/inits/tag/go.php";
  elseif ( $padTag  [$pad] == 'make'     ) return include "sequence/inits/tag/go.php";
  elseif ( $padTag  [$pad] == 'remove'   ) return include "sequence/inits/tag/go.php";
  elseif ( $padTag  [$pad] == 'sequence' ) return include "sequence/inits/tag/go.php";
  elseif ( $padTag  [$pad] == 'store'    ) return include "sequence/inits/tag/go.php";

      if ( $padType [$pad] == 'action'   ) return include "sequence/inits/type/go.php";
  elseif ( $padType [$pad] == 'keep'     ) return include "sequence/inits/type/go.php";
  elseif ( $padType [$pad] == 'make'     ) return include "sequence/inits/type/go.php";
  elseif ( $padType [$pad] == 'remove'   ) return include "sequence/inits/type/go.php";
  elseif ( $padType [$pad] == 'sequence' ) return include "sequence/inits/type/go.php";
  elseif ( $padType [$pad] == 'store'    ) return include "sequence/inits/type/go.php";
    
?>