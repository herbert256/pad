<?php

  if     ( isset ( $pqStore [$pqTag]                     ) ) $pqPull   = $pqTag;
  elseif ( file_exists ( "sequence/types/$pqTag"             ) ) $pqSeq    = $pqTag;
  elseif ( file_exists ( "sequence/actions/types/$pqTag.php" ) ) $pqAction = $pqTag;

?>