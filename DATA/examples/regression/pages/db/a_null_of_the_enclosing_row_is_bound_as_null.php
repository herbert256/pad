<?php

  // A NULL column of the enclosing row is NULL in the SQL it goes into - a $name of a
  // database tag, the relation a nested select follows - where it was bound as the empty
  // text '': a boss of NULL found the row whose key is '', on MySQL an integer key 0.

  $padSelect ['staffBoss']   = [ 'db' => 'staff' ];
  $padSelect ['staffByBoss'] = [ 'db' => 'staff' ];

  $padRelations ['staffByBoss'] ['staffBoss'] = [ 'name' => 'boss' ];

?>
