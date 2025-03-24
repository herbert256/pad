<?php

  foreach ( $padSeqPlays as $padSeqPlay ) {

    extract ( $padSeqPlay );

    if ( is_numeric ($padSeqParm) )
      if ( str_contains ( $padSeqParm, '.' ) )  $padSeqParm = doubleval ( $padSeqParm );
      else                                      $padSeqParm = intval ( $padSeqParm );

    $padSeqSave = $padSeq;
    $padSeqLoop = $padSeq;

    if ( $padSeqPlay == 'store' )
      include 'sequence/plays/store.php';

    $padSeq = include 'sequence/build/call.php';

    if     ( $padSeqPlay == 'keep'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'keep'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqPlay == 'keep'   and $padSeq <> $padSeqLoop ) return FALSE;
    elseif ( $padSeqPlay == 'keep'                              ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'remove' and $padSeq === TRUE       ) return FALSE;
    elseif ( $padSeqPlay == 'remove' and $padSeq === FALSE      ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'remove' and $padSeq == $padSeqLoop ) return FALSE;
    elseif ( $padSeqPlay == 'remove'                            ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'make'   and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqPlay == 'make'   and $padSeq === TRUE       ) $padSeq = $padSeqSave;
    elseif ( $padSeqPlay == 'store'  and $padSeq === FALSE      ) return FALSE;
    elseif ( $padSeqPlay == 'store'  and $padSeq === TRUE       ) $padSeq = $padSeqSave;

  }

  return $padSeq;

?>