<?php

  // A table declared with its own rows: the page is cut by those rows, and the pager counts
  // its pages by them too - it took the tag's rows= or 10, and with four rows of one per
  // page saw a single page of ten and wrote no links at all.

  $padSelect ['staffByOne'] = [ 'db' => 'staff', 'key' => 'name', 'order' => 'name', 'rows' => 1 ];

  $pg = 2;

?>
