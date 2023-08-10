<?php

  $padSeqLoopCheck = ceil ( $padSeqLoop / $padSeqMultiple) * $padSeqMultiple;

  return ( $padSeqLoopCheck == $padSeqLoop );

?>