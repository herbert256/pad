<?php

  // The page format/a_csv_answer_of_a_record_or_of_objects_is_a_table asks for CSV: a record
  // holding a list, and rows as json_decode gives them, objects.

  $order     = [ 'id' => 7, 'customer' => 'Ann', 'tags' => [ 'a', 'b' ] ];
  $rows      = json_decode ( '[{"id":1,"name":"Ann"},{"id":2,"name":"Bob","tags":["x"]}]' );
  $padExpose = [ padRequest ( 'which', 'order' ) == 'rows' ? 'rows' : 'order' ];

?>
