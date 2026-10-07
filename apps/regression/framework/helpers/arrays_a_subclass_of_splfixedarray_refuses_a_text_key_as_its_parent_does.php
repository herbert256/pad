<?php

  // A class extending SplFixedArray that hands offsetExists and offsetGet to its parent
  // refuses a text key the way SplFixedArray does: a key it does not have. PHP's refusal is
  // then raised in the application's file, not where arr.php asked, and was rethrown as a
  // bug of the object's own.

  class ArrRefusedKeyFixedRows extends SplFixedArray {
    public function offsetExists ( $key ): bool { return parent::offsetExists ( $key ); }
    public function offsetGet ( $key ): mixed { return parent::offsetGet ( $key ); }
  }

  $rows      = new ArrRefusedKeyFixedRows ( 2 );
  $rows [1]  = 'b';
  $data      = [ 'l' => $rows, 'n' => 1 ];

  padArrForget ( $data, 'l.name, n' );

  $r = json_encode ( [
    padArrGet ( [ 'l' => $rows ], 'l.name', 'none' ),
    padArrGet ( [ 'l' => $rows ], 'l.1' ),
    padArrHas ( [ 'l' => $rows ], 'l.name' ),
    array_keys ( $data )
  ] );

  unset ( $rows, $data );

?>
