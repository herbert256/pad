<?php

    $padSeqLoop = (1 << $padSeqLoop) + 1;
    $padSeqLoop = $padSeqLoop * $padSeqLoop;
    $padSeqLoop = $padSeqLoop - 2;

    return $padSeqLoop; 

?>