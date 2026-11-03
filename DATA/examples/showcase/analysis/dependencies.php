<?php
  $tasks = [ [ 'id' => 'Plan', 'duration' => 2 ], [ 'id' => 'Build', 'duration' => 5 ], [ 'id' => 'Write', 'duration' => 2 ], [ 'id' => 'Test', 'duration' => 3 ], [ 'id' => 'Ship', 'duration' => 1 ] ];
  $dependencies = [ [ 'Plan', 'Build' ], [ 'Plan', 'Write' ], [ 'Build', 'Test' ], [ 'Write', 'Test' ], [ 'Test', 'Ship' ] ];
  $dependencyResult = padGraph ( 'criticalpath', $tasks, $dependencies );
  $taskRows = [];
  foreach ( $dependencyResult ['tasks'] as $task ) $taskRows [] = $task + [ 'group' => $task ['critical'] ? 'Critical path' : 'Has slack' ];
?>
