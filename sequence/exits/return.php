<?php

  if ( count ( $padSeqReturn ) )
    $padSeqResult = $padSeqReturn;

  $padSeqReturn = [];

  $padSeqI = -1;

  foreach ($padSeqResult as $padK  ) {
    $padSeqI++;
    $padSeqReturn [$padSeqI] ['sequence'] = $padSeqReturn [$padSeqI] [$padSeqName] = $padK; 
  }

?>