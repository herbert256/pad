<?php

  // PHP's "Cannot access offset of type ..." raised inside the application's own offsetGet
  // - here indexing a text with a text key - is a bug of the object's own and is reported.
  // Only SplFixedArray's refusal of a key's type, its own or a subclass's through parent::,
  // is a key that is not there; any TypeError with those words was taken for one, and the
  // bug hid behind the default.

  class ArrRefusedKeyOwnOffsetRows implements ArrayAccess {
    private $title = 'text';
    public function offsetExists ( $key ): bool { return TRUE; }
    public function offsetGet ( $key ): mixed { return $this->title [$key]; }
    public function offsetSet ( $key, $value ): void {}
    public function offsetUnset ( $key ): void {}
  }

  $title = padArrGet ( [ 's' => new ArrRefusedKeyOwnOffsetRows ], 's.title', 'none' );

?>
