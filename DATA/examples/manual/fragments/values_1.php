<?php

  // A form came back: which fields did the visitor fill in?
  // PHP's empty() calls the zero quantity empty and the spaces
  // filled - padBlank calls them the other way round.

  $posted = [ 'name'     => 'Ann',
              'quantity' => '0',
              'remarks'  => '   ',
              'coupon'   => NULL,
              'tags'     => [] ];

  $answers = [];

  foreach ( $posted as $name => $value )
    $answers [] = [
      'name'  => $name,
      'value' => json_encode ( $value ),
      'blank' => padBlank ( $value ) ? 'blank' : 'filled',
      'empty' => empty ( $value ) ? 'empty' : 'not empty'
    ];

?>
