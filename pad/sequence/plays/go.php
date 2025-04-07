<?php

  foreach ( $padSeqPlays as $padSeqPlay ) {

    extract ( $padSeqPlay );

    if ( isset ( $padSeqStore [$padSeqParm] ) ) {
      include 'sequence/plays/store.php';
    }

    if ( is_numeric ($padSeqParm) )
      if ( str_contains ( $padSeqParm, '.' ) )  $padSeqParm = doubleval ( $padSeqParm );
      else                                      $padSeqParm = intval ( $padSeqParm );

    $padSeqSave = $padSeq;
    $padSeqLoop = $padSeq;

    $padSeq = include 'sequence/build/call.php';

    if     ( $padSeqPlay == 'keep'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'keep'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqPlay == 'keep'   and $padSeq <> $padSeqLoop ) return FALSE;
    elseif ( $padSeqPlay == 'keep'                              ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'remove' and $padSeq === TRUE       ) return FALSE;
    elseif ( $padSeqPlay == 'remove' and $padSeq === FALSE      ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'remove' and $padSeq == $padSeqLoop ) return FALSE;
    elseif ( $padSeqPlay == 'remove'                            ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'flag'   and $padSeq === TRUE       ) $padSeq = 1;
    elseif ( $padSeqPlay == 'flag'   and $padSeq === FALSE      ) $padSeq = 0;
    elseif ( $padSeqPlay == 'flag'   and $padSeq == $padSeqLoop ) $padSeq = 1;
    elseif ( $padSeqPlay == 'flag'                              ) $padSeq = 0;
    elseif ( $padSeqPlay == 'make'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqPlay == 'make'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;

  }

  return $padSeq;

?>