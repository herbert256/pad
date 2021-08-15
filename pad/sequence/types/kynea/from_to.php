<?php

    $pad_sequence = (1 << $pad_sequence) + 1;
    $pad_sequence = $pad_sequence * $pad_sequence;
    $pad_sequence = $pad_sequence - 2;

    return $pad_sequence; 

?>