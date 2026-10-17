<?php

  // MySQL and MariaDB run what an executable comment holds - /*!...*/, and MariaDB's own
  // /*M!...*/ - so for the replay's check a statement holding one is a write when a write word
  // stands in it: one starting with such a comment was read past it as a plain comment, and a
  // /*M!...*/ in a WITH was skipped by the walk - mysqldump's ALTER TABLE ... DISABLE KEYS
  // went to the database during a replay. A plain comment in front still leaves a read a read.

  $replayShown = implode ( ' ', array_map ( fn ( $sql ) => padReplayWrites ( $sql ) ? 'write' : 'read', [
    '/*!40000 ALTER TABLE t DISABLE KEYS */',
    '/*! delete from t */',
    '/*!50000 delete*/ from t where 1',
    '/*M!100100 delete from t */',
    'with x as (select 1) /*M!100000 update*/ t set a = 1',
    '/* delete */ select * from t',
    'select 1' ] ) );

?>
