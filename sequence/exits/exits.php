<?php

  if ( file_exists ( "/pad/sequence/types/$padSeqSeq/exit.php" ) )   
    include "/pad/sequence/types/$padSeqSeq/exit.php";    

  include '/pad/sequence/exits/actions.php';
  include '/pad/sequence/exits/data.php';
  include '/pad/sequence/exits/store.php';
  include '/pad/sequence/exits/return.php';
  include '/pad/sequence/exits/done.php';
   
  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/sequence.php';

  foreach ( $GLOBALS as $padK => $padV )
    if ( str_starts_with ( $padK, 'padSeq') )
      if ( $padK <> 'padSeqStore' and $padK <> 'padSeqReturn' )
        unset ( $GLOBALS [$padK] );

?>