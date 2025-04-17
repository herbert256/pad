<?php

  $padPrmName  = $pqSeq;
  $padPrmValue = $pqParm;
  
  if     ( pqPlay ( $pqTag )  ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                                  $pqPlay = 'make';

  include 'sequence/plays/add.php';

  $pqSeq  = '';
  $pqParm = '';

?>