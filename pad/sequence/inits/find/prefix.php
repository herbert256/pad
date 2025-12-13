<?php

  if ( $pqPrefix )
    if     ( isset ( $pqStore [$pqPrefix] ) ) $pqPull   = $pqPrefix;
    elseif ( pqSeq ( $pqPrefix )            ) $pqSeq    = $pqPrefix;
    elseif ( pqAction ( $pqPrefix )         ) $pqAction = $pqPrefix;

?>