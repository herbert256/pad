<?php

  $a = NULL;
  padArrSet ( $a, 'user.address.city', 'Leiden' );
  $made = json_encode ( $a );

  $b = [ 'user' => 'plain' ];
  $over = json_encode ( padArrSet ( $b, 'user.name', 'Ann' ) );

  $c = [ 'items' => [ [ 'n' => 1 ], [ 'n' => 2 ] ] ];
  padArrSet ( $c, 'items.*.done', TRUE );
  $star = json_encode ( $c );

  $d = [ 'list' => [ 'a' ] ];
  padArrSet ( $d, 'list.1', 'b' );
  $list = json_encode ( $d );

  $e = [ 1, 2 ];
  $whole = json_encode ( padArrSet ( $e, NULL, [ 'replaced' ] ) );

  $f = new stdClass;
  padArrSet ( $f, 'mail.from', 'shop@example.com' );
  $object = json_encode ( $f );

  $g = new ArrayObject ( [] );
  $h = [ 'settings' => $g ];
  padArrSet ( $h, 'settings.theme', 'dark' );
  $offset = json_encode ( $g -> getArrayCopy () );

  $i = [];
  $answer = json_encode ( padArrSet ( $i, 'x.0', 0 ) );

?>
