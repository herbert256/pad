<?php

  // MySQL runs what an executable comment holds, so a WITH with /*!update*/ in it is a write
  // for the replay's check; the walk skipped it as a comment. Ordinary reads stay reads.

  $replayShown = implode ( ' ', array_map ( fn ( $sql ) => padReplayWrites ( $sql ) ? 'write' : 'read', [
    'with x as (select 1) /*!update*/ t set a = 1',
    'with x as (select 1) select * from x',
    "with x as (select 'update') select * from x" ] ) );

?>
