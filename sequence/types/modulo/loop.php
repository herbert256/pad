<?php

  if ( ! $padSeqParm )
    $padSeqParm = 1;

  return $padSeqLoop % (int) $padSeqParm;

?>