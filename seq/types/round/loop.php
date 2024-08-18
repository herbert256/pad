<?php

  if ( ! $padSeqRound )
    $padSeqRound = 1;

  return round ( $padSeqLoop / $padSeqRound ) * $padSeqRound;

?>