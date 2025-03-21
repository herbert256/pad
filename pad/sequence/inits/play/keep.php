<?php

    if     ( $padSeq === TRUE       ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeq === FALSE      ) continue;
    elseif ( $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeq <> $padSeqLoop ) continue;

?>