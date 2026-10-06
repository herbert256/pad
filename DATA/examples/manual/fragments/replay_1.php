<?php

  $statements = [];

  foreach ( [ 'select name from staff', 'check staff where id = 1', 'insert into log values (1)', 'update staff set salary = 0' ] as $sql )
    $statements [] = [ 'sql' => $sql, 'replay' => padReplayWrites ( $sql ) ? 'refused' : 'runs' ];

?>
