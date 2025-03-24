<?php

  $padStartType = 'tag';
  
  $padSeqInfo ['tags'] [] = $padSeqTag;

      if ( $padSeqTag == 'action'   ) return include "sequence/inits/tag/action.php";
  elseif ( $padSeqTag == 'keep'     ) return include "sequence/inits/tag/play.php";
  elseif ( $padSeqTag == 'make'     ) return include "sequence/inits/tag/play.php";
  elseif ( $padSeqTag == 'remove'   ) return include "sequence/inits/tag/play.php";
  elseif ( $padSeqTag == 'sequence' ) return include "sequence/inits/tag/sequence.php";
  elseif ( $padSeqTag == 'store'    ) return include "sequence/inits/tag/store.php";

?>