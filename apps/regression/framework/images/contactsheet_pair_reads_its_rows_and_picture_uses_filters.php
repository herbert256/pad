<?php

$sheet = padUnprotect ( padUnescape ( padCode ( '{image:contactsheet width=16, height=16, columns=1}[{"src":"assets/image-focus.png","caption":"First"}]{/image:contactsheet}' ) ) );
$picture = padUnprotect ( padUnescape ( padCode ( "{image:picture 'assets/image-focus.png', width=16, widths='8,16,32', filter='grayscale', alt='Grey'/}" ) ) );
preg_match ( '/src="([^"]+)"/', $picture, $match );
$image = imagecreatefrompng ( dirname ( APPS ) . '/www/regression/framework/_thumbs/' . basename ( $match [1] ) );
$rgb = imagecolorat ( $image, 2, 2 );
$answer = str_contains ( $sheet, '<li>First</li>' ) && ( ( $rgb >> 16 ) & 255 ) === ( ( $rgb >> 8 ) & 255 ) && ( $rgb & 255 ) === ( ( $rgb >> 8 ) & 255 )
  ? 'paired rows and responsive image filters render' : 'FAIL';

?>
