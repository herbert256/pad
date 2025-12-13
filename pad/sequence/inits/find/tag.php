<?php

  if     ( isset ( $pqStore [$pqTag] ) ) $pqPull   = $pqTag;
  elseif ( pqSeq ( $pqTag )            ) $pqSeq    = $pqTag;
  elseif ( pqAction ( $pqTag )         ) $pqAction = $pqTag;

?>