<?php

  // Written statements a replay must not send, the verb behind a comment or a WITH: each
  // answers 0, as a refused write does, instead of going to the database.

  if ( padReplaying () ) {
    $sql = [];
    foreach ( [ "/* note */ update no_such_table set x = 1",
                "-- note\nupdate no_such_table set x = 1",
                "# note\ndelete from no_such_table",
                "with t as (select 1) delete from no_such_table" ] as $one )
      $sql [] = var_export ( db ( $one ), TRUE );
    $sql = implode ( ' ', $sql );
  } else
    $sql = 'not replayed';

?>
