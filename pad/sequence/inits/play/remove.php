<?php

    if     ( $padSeq === TRUE       ) continue;
    elseif ( $padSeq === FALSE      ) $padSeqFixed [] = $padSeq;;
    elseif ( $padSeq == $padSeqLoop ) continue;
    elseif ( $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

?>