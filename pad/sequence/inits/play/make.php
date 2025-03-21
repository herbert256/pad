<?php

    if     ( $padSeq === TRUE       ) continue;
    elseif ( $padSeq === FALSE      ) continue;
    elseif ( $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

?>