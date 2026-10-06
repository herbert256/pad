<?php

  // padArrUndot builds nested arrays: a value on the way that is no array is replaced, as
  // a plain one is. An object there - a key holding one and a longer key under it - was
  // written into, so the caller's object changed, or one without such a property was an
  // error.

  $settings    = new stdClass;
  $settings->x = 1;

  $point = new class { public $x = 1; };

  $r = json_encode ( [
    padArrUndot ( [ 'a' => $settings, 'a.b' => 2 ] ),
    $settings,
    padArrUndot ( [ 'p' => $point, 'p.y' => 2 ] ),
    padArrUndot ( [ 'a.b' => 2, 'a' => $settings ] ) ['a'] === $settings
  ] );

  unset ( $settings, $point );

?>
