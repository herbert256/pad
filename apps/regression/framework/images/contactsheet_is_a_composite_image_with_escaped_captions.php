<?php

include_once PAD . 'subs/images/_lib/img.php';
include_once PAD . 'subs/images/_lib/views.php';
$rows = [ [ 'src' => 'assets/image-focus.png', 'caption' => '<First>' ], [ 'src' => 'assets/image-focus.png', 'caption' => 'Second & third' ] ];
$html = padImageContactSheet ( $rows, [ 'alt' => 'Two images', 'width' => 16, 'height' => 16, 'format' => 'png', 'focusx' => 100 ], 2 );
preg_match ( '/src="([^"]+)"/', $html, $match );
$path = dirname ( APPS ) . '/www/regression/framework/_thumbs/' . basename ( $match [1] );
$info = getimagesize ( $path ); $image = imagecreatefrompng ( $path );
$again = padImageContactSheet ( $rows, [ 'alt' => 'Two images', 'width' => 16, 'height' => 16, 'format' => 'png', 'focusx' => 100 ], 2 );
$answer = $info [0] === 56 && $info [1] === 32 && imagecolorat ( $image, 16, 16 ) === 0x0000ff && imagecolorat ( $image, 40, 16 ) === 0x0000ff
  && str_contains ( $again, $match [1] ) && substr_count ( $html, '<img ' ) === 1
  && str_contains ( $html, '&lt;First&gt;' ) && str_contains ( $html, 'Second &amp; third' )
  ? 'one cached composite with real tiles and escaped captions' : 'FAIL';

?>
