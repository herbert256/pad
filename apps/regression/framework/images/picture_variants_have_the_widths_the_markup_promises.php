<?php

include_once PAD . 'subs/images/_lib/img.php';
include_once PAD . 'subs/images/_lib/views.php';
$html = padImg ( 'assets/image-focus.png', [ 'alt' => 'Both colours', 'width' => 16, 'format' => 'png', '_picture' => TRUE, 'widths' => [ 8, 16, 32, 64 ], 'sizes' => '100vw' ] );
preg_match ( '/srcset="([^"]+)"/', $html, $set );
$widths = [];
foreach ( explode ( ', ', $set [1] ?? '' ) as $item ) {
  preg_match ( '/(.+) (\d+)w$/', $item, $match );
  $info = getimagesize ( dirname ( APPS ) . '/www/regression/framework/_thumbs/' . basename ( $match [1] ) );
  if ( $info [0] === (int) $match [2] ) $widths [] = $info [0];
}
$answer = $widths === [ 8, 16, 32 ] && substr_count ( $html, '<source ' ) === 3
  && str_contains ( $html, 'sizes="100vw"' ) && str_contains ( $html, '<picture ' )
  ? 'three real width variants and no upscaling' : 'FAIL';

?>
