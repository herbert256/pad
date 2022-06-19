<?php

    $pad_seq_loop = (1 << $pad_seq_loop) + 1;
    $pad_seq_loop = $pad_seq_loop * $pad_seq_loop;
    $pad_seq_loop = $pad_seq_loop - 2;

    return $pad_seq_loop; 

?>