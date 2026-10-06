<?php

  if ( padReplaying () ) {
    $sql  = var_export ( db ( "update no_such_table set x = 1" ), TRUE );
    $file = var_export ( padFilePut ( 'replay-never/written.txt', 'x' ), TRUE ) . ', '
          . ( file_exists ( DATA . 'replay-never/written.txt' ) ? 'written' : 'not written' );
  } else
    $sql = $file = 'not replayed';

?>
