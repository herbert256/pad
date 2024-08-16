<?php

  if ( ! $padSeqXor )
    $padSeqXor = 1;

  return $padSeqLoop ^ (int) $padSeqXor;

?>