<?php

      if ( $padSeqType == 'action'   ) return include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'keep'     ) return include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'make'     ) return include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'remove'   ) return include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'sequence' ) return include "sequence/inits/type/go.php";
  elseif ( $padSeqType == 'store'    ) return include "sequence/inits/type/go.php";
    
      if ( $padSeqTag  == 'action'   ) return include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'keep'     ) return include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'make'     ) return include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'remove'   ) return include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'sequence' ) return include "sequence/inits/tag/go.php";
  elseif ( $padSeqTag  == 'store'    ) return include "sequence/inits/tag/go.php";

?>