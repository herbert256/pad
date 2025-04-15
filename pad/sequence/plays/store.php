<?php

  $padStoreIndex = ( $padSeqPlayType == 'build' ) ? count ( $padSeqResult ) : count ( $padSeqFixed );
  $padSeqParm    = $padSeqStore [$padSeqParm] [$padStoreIndex];
  
?>