<?php

    $padSeq_loop = (1 << $padSeq_loop) + 1;
    $padSeq_loop = $padSeq_loop * $padSeq_loop;
    $padSeq_loop = $padSeq_loop - 2;

    return $padSeq_loop; 

?>