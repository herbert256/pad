<?php

  // A relation declared empty - the form classicModels once wrote every relation in - names
  // no field to follow, so it relates nothing, and the strict check says so as for a
  // relation never declared. It counted as one: every row met every row without a word.

  $padSelect ['outerSel'] = [ 'db' => 'staff', 'key' => 'name' ];
  $padSelect ['innerSel'] = [ 'db' => 'staff', 'key' => 'name' ];

  $padRelations ['innerSel'] ['outerSel'] = [];

?>
