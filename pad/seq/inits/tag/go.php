<?php

      if ( $padTag  [$pad] == 'action'   ) return include "seq/inits/tag/operation.php";
  elseif ( $padTag  [$pad] == 'keep'     ) return include "seq/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'make'     ) return include "seq/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'one'      ) return include "seq/inits/tag/operation.php";
  elseif ( $padTag  [$pad] == 'remove'   ) return include "seq/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'sequence' ) return include "seq/inits/tag/sequence.php";
  elseif ( $padTag  [$pad] == 'store'    ) return include "seq/inits/tag/store.php";

?>