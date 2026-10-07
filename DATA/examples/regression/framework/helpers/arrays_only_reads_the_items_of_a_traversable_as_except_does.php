<?php

  // A Traversable is a set like an array - padArrExcept reads its items - but padArrOnly
  // looked for the keys in it as in an object's properties, and a generator or an
  // IteratorAggregate answered nothing.

  $generator = ( function () { yield 'id' => 7; yield 'name' => 'Ann'; yield 'email' => 'ann@example.com'; } ) ();

  $aggregate = new class implements IteratorAggregate {
    private $row = [ 'id' => 8, 'name' => 'Bob', 'email' => 'bob@example.com' ];
    public function getIterator (): Iterator { return new ArrayIterator ( $this->row ); }
  };

  $r = json_encode ( [
    padArrOnly ( $generator, 'email, id' ),
    padArrOnly ( $aggregate, 'name' ),
    padArrExcept ( $aggregate, 'email' ),
    padArrOnly ( new ArrayObject ( [ 'a' => 1, 'b' => 2 ] ), 'b' )
  ] );

  unset ( $generator, $aggregate );

?>
