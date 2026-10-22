<?php

  // A select nested in a select of one value - type='field' - has no row to follow, and is
  // told so: the field select was passed over as one rendering its @else@, and the nested
  // select listed every row of its table without a word.

  $padSelect ['outerSel'] = [ 'db' => 'staff', 'key' => 'name' ];
  $padSelect ['innerSel'] = [ 'db' => 'staff', 'key' => 'name' ];

  $padRelations ['innerSel'] ['outerSel'] = 'name';

?>
