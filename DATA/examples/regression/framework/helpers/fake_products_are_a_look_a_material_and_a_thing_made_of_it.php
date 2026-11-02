<?php

  // padFake ( 'product' ) is a look, a material and a thing - the material always one the
  // thing is made of here, never a glass blanket or a silk teapot - varied enough to fill a
  // shop's list; the same seed the same product.

  $soft  = [ 'Cotton', 'Fleece', 'Linen', 'Silk', 'Velvet', 'Wool', 'Denim', 'Nylon', 'Canvas', 'Paper' ];
  $hard  = [ 'Glass', 'Steel', 'Ceramic', 'Enamel', 'Stoneware', 'Brass', 'Cast Iron', 'Oak', 'Pine', 'Walnut', 'Bamboo' ];
  $woven = [ 'Rattan', 'Seagrass', 'Willow' ];

  padFakeSeed ( 12 );

  $shaped = $sensible = 0;
  $seen   = [];

  for ( $i = 0; $i < 500; $i++ ) {

    $product = padFake ( 'product' );

    $shaped += preg_match ( '/^[A-Z][a-z]+ (Cast Iron|[A-Z][a-z]+) (Candle Holder|[A-Z][a-z]+)$/D', $product, $match ) ? 1 : 0;

    $material = $match [1] ?? '';
    $thing    = $match [2] ?? '';

    $sensible += match ( TRUE ) {
      in_array ( $thing, [ 'Blanket', 'Cushion', 'Scarf' ] )            => in_array ( $material, $soft ),
      in_array ( $thing, [ 'Mug', 'Teapot', 'Vase', 'Bottle', 'Lamp' ] ) => in_array ( $material, $hard ),
      $thing == 'Basket'                                                 => in_array ( $material, $woven ),
      default                                                            => $material !== '',
    } ? 1 : 0;

    $seen [$product] = TRUE;

  }

  padFakeSeed ( 'q' );  $first  = padFake ( 'product' );
  padFakeSeed ( 'q' );  $second = padFake ( 'product' );

  $r = json_encode ( [
    'shaped'   => $shaped,
    'sensible' => $sensible,
    'varied'   => count ( $seen ) > 300,
    'seed'     => $first === $second,
  ] );

?>
