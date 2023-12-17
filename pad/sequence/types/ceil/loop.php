<?php

  if ( ! $padSeqCeil )
    $padSeqCeil = 1;

  return ceil ( $padSeqLoop / (int) $padSeqCeil ) * $padSeqCeil;

?>