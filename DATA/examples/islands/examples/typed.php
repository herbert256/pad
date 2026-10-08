<?php

  // An order as a page's PHP makes it - from a database, here an array. The island gets it as
  // its props, and its component, in TypeScript, knows its shape from the types pad types
  // wrote: the sample of this page, captured from this very data.

  $order = [ 'number'   => 1042,
             'status'   => 'shipped',
             'paid'     => TRUE,
             'note'     => NULL,
             'customer' => [ 'name' => 'Ada Fernández', 'city' => 'Amsterdam' ],
             'lines'    => [ [ 'product' => 'Aurora Headphones', 'quantity' => 1, 'price' => 189.0 ],
                             [ 'product' => 'Tactile Keyboard',  'quantity' => 2, 'price' => 119.0, 'gift' => TRUE ] ] ];

  $padExpose = [ 'order' ];

?>
