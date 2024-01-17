<?php

  if ( ! $padSeqModulo )
    $padSeqModulo = 1;

  return $padSeqLoop % (int) $padSeqModulo;

?>