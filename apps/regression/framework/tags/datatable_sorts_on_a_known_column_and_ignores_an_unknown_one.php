<?php

  // The links of a sortable table set these values: the first table is asked to sort on its
  // total, high to low; the second, with its own prefix, on a column it does not show.

  $_GET ['sort']   = 'total';
  $_GET ['dir']    = 'desc';
  $_GET ['b_sort'] = 'secret';

  $rows = [
    [ 'name' => 'Ann',  'total' => 5,  'secret' => 3 ],
    [ 'name' => 'Bob',  'total' => 12, 'secret' => 1 ],
    [ 'name' => 'Cleo', 'total' => 5,  'secret' => 2 ],
    [ 'name' => 'Dirk', 'total' => 8,  'secret' => 4 ],
  ];

?>
