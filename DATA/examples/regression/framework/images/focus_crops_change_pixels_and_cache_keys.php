<?php

include_once PAD . 'subs/images/_lib/img.php';
$opts = [ 'alt' => 'red and blue', 'width' => 16, 'height' => 16, 'format' => 'png', '_result' => TRUE ];
$left = padImg ( 'assets/image-focus.png', $opts + [ 'focusx' => 0 ] );
$right = padImg ( 'assets/image-focus.png', $opts + [ 'focusx' => 100 ] );
$root = dirname ( APPS ) . '/www/regression/framework/_thumbs/';
$a = imagecreatefrompng ( $root . basename ( $left [0] ) );
$b = imagecreatefrompng ( $root . basename ( $right [0] ) );
$answer = $left [0] !== $right [0] && imagecolorat ( $a, 8, 8 ) === 0xff0000 && imagecolorat ( $b, 8, 8 ) === 0x0000ff
  && padImgSizes ( 32, 16, 16, 16, 'contain', 0, 100 ) [0] === [ 0, 0, 32, 16 ]
  ? 'focal crops preserve the selected pixels and cache separately' : 'FAIL';

?>
