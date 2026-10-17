<?php

  // An object a page leaves that can be read as a list again - an IteratorAggregate, an
  // ArrayObject - stays that object after a tag read it as a list; only an iterator that runs
  // once is replaced by its rows. Replaced, a later {$cart.items.1.name} was not found and a
  // function calling $cart->total() met an array.

  class VariablesKeptCart implements IteratorAggregate {

    public $items = [ [ 'name' => 'Pen', 'price' => 2 ], [ 'name' => 'Ink', 'price' => 5 ] ];

    function getIterator (): Iterator { return new ArrayIterator ( $this -> items ); }

    function total () { return array_sum ( array_column ( $this -> items, 'price' ) ); }

  }

  function variablesKeptCartTotal () { return $GLOBALS ['cart'] -> total (); }

  function variablesKeptListKind () { return get_class ( $GLOBALS ['list'] ); }

  $cart = new VariablesKeptCart;
  $list = new ArrayObject ( [ [ 'n' => 1 ], [ 'n' => 2 ] ] );

?>
