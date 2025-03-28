<?php

      if ( $padSeqType == 'action'   ) include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'keep'     ) include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'make'     ) include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'remove'   ) include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'sequence' ) include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'store'    ) include "sequence/inits/type/go.php";
    
  elseif ( $padSeqTag  == 'action'   ) include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'keep'     ) include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'make'     ) include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'remove'   ) include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'sequence' ) include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'store'    ) include "sequence/inits/tag/go.php";

      if ( $padSeqSeq == 'start' )                         return;
  elseif ( $padSeqSeq == 'store' )                         return;
  elseif ( ! $padSeqSeq          )                         return include "sequence/inits/tag/sequence.php";
  elseif ( ! file_exists ( "sequence/types/$padSeqSeq" ) ) return include "sequence/inits/tag/sequence.php";
  
?>