<?php

include_once PAD . 'subs/images/avatars/_lib/avatar.php';
include_once PAD . 'subs/images/avatars/_lib/views.php';
$views = [];
foreach ( [ 'rings', 'mosaic', 'beam' ] as $style )
  $views [] = padAvatarGeometric ( 'Ada', 64, $style, '<script>"&' );
$answer = count ( array_unique ( $views ) ) === 3
  && $views [0] === padAvatarGeometric ( ' ADA ', 64, 'rings', '<script>"&' )
  && ! str_contains ( implode ( '', $views ), '<script>' )
  && str_contains ( $views [1], '&lt;script&gt;&quot;&amp;' )
  && substr_count ( $views [1], '<path ' ) === 16 ? 'stable distinct escaped artwork' : 'FAIL';

?>
