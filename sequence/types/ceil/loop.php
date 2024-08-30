<?php

  if ( ! $padSeqParm )
    $padSeqParm = 1;

  return ceil ( $padSeqLoop / (int) $padSeqParm ) * $padSeqParm;

?>