<?php

  $padSeqInfo ['tags'] [] = $padTag [$pad];

      if ( $padTag  [$pad] == 'action'   ) return include "sequence/inits/tag/operation.php";
  elseif ( $padTag  [$pad] == 'keep'     ) return include "sequence/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'make'     ) return include "sequence/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'one'      ) return include "sequence/inits/tag/operation.php";
  elseif ( $padTag  [$pad] == 'remove'   ) return include "sequence/inits/tag/play.php";
  elseif ( $padTag  [$pad] == 'sequence' ) return include "sequence/inits/tag/sequence.php";
  elseif ( $padTag  [$pad] == 'store'    ) return include "sequence/inits/tag/store.php";

?>