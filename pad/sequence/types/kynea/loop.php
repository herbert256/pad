<?php

    $pSeq_loop = (1 << $pSeq_loop) + 1;
    $pSeq_loop = $pSeq_loop * $pSeq_loop;
    $pSeq_loop = $pSeq_loop - 2;

    return $pSeq_loop; 

?>