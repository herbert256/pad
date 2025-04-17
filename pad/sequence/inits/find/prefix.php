<?php

  if ( $pqPrefix )
    if     ( isset ( $pqStore [$pqPrefix]                     ) ) $pqPull   = $pqPrefix;
    elseif ( file_exists ( "sequence/types/$pqPrefix"             ) ) $pqSeq    = $pqPrefix;
    elseif ( file_exists ( "sequence/actions/types/$pqPrefix.php" ) ) $pqAction = $pqPrefix;

?>