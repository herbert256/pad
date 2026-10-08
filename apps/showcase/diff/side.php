<?php

  $before = <<<'TEXT'
function total ( $rows ) {

  $sum = 0;

  foreach ( $rows as $row )
    $sum += $row ['price'];

  return $sum;

}
TEXT;

  $after = <<<'TEXT'
function total ( $rows, $tax = 0.21 ) {

  $sum = 0;

  foreach ( $rows as $row )
    $sum += $row ['price'] * $row ['quantity'];

  return round ( $sum * ( 1 + $tax ), 2 );

}
TEXT;

?>
