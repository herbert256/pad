<?php

  // A TypeError raised inside the application's own offsetGet is its bug and is reported;
  // only PHP refusing a key's type where the helper asks for it (SplFixedArray and a text
  // key) is a key that is not there. Every TypeError was taken for that, and the bug was
  // hidden behind the default.

  class ArrRefusedKeyBuggyRows implements ArrayAccess {
    private $rows = [ 'name' => [ 'first' => 'Ann' ] ];
    public function offsetExists ( $key ): bool { return isset ( $this->rows [$key] ); }
    public function offsetGet ( $key ): mixed { return str_repeat ( $this->rows [$key], 2 ); }
    public function offsetSet ( $key, $value ): void {}
    public function offsetUnset ( $key ): void {}
  }

  $first = padArrGet ( [ 'user' => new ArrRefusedKeyBuggyRows ], 'user.name.first', 'none' );

?>
