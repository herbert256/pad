<?php 

  if ( $padSeqParm and isset ( $padSeqStore [$padSeqParm] ) )
    include 'sequence/plays/store.php';

  if ( is_numeric ($padSeqParm) )
    if ( str_contains ( $padSeqParm, '.' ) ) $padSeqParm = doubleval ( $padSeqParm );
    else                                     $padSeqParm = intval    ( $padSeqParm );

  include 'sequence/build/include.php';

  $padSeq = include 'sequence/build/call.php';

  if     ( $padSeqPlay == 'make'   and $padSeq === TRUE       ) $padSeq = $padSeqLoop;

  elseif ( $padSeqPlay == 'keep'   and $padSeq === TRUE       ) $padSeq = $padSeqLoop;
  elseif ( $padSeqPlay == 'keep'   and $padSeq <> $padSeqLoop ) $padSeq = FALSE;

  elseif ( $padSeqPlay == 'remove' and $padSeq === TRUE       ) $padSeq = FALSE;
  elseif ( $padSeqPlay == 'remove' and $padSeq === FALSE      ) $padSeq = $padSeqLoop;
  elseif ( $padSeqPlay == 'remove' and $padSeq == $padSeqLoop ) $padSeq = FALSE;

  elseif ( $padSeqPlay == 'flag'   and $padSeq === TRUE       ) $padSeq = 1;
  elseif ( $padSeqPlay == 'flag'   and $padSeq === FALSE      ) $padSeq = 0;
  elseif ( $padSeqPlay == 'flag'   and $padSeq == $padSeqLoop ) $padSeq = 1;
  elseif ( $padSeqPlay == 'flag'   and $padSeq <> $padSeqLoop ) $padSeq = 0;

  if ( ! isset ( $padSeqInfo ['plays'] ) or ! in_array ( "$padSeqPlay/$padSeqSeq", $padSeqInfo ['plays'] ) )
    $padSeqInfo ['plays'] [] = "$padSeqPlay/$padSeqSeq";

?>