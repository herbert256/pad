<?php

include_once PAD . 'subs/images/avatars/_lib/avatar.php';
include_once PAD . 'subs/images/avatars/_lib/identicon.php';
include_once PAD . 'subs/images/avatars/_lib/views.php';
$unsafe = [ 'javascript:alert(1)', '//example.org/a.png', 'https://user:pass@example.org/a.png', '../outside.jpg', '%2e%2e/outside.jpg', "https://example.org/\nfile.png", 'data:image/svg+xml,<svg/>' ];
$ok = TRUE;
foreach ( $unsafe as $src )
  $ok = $ok && padAvatarPhotoSource ( $src ) === '';
$missing = padAvatarPhoto ( 'missing.jpg', 'Ada Lovelace', 48, 'initials' );
$local = padAvatarPhoto ( 'assets/wide.png', '<Ada>', 48, 'identicon' );
$remote = padAvatarPhoto ( 'https://example.org/photo.png?a=1&b=2', 'Ada', 48, 'geometric' );
$answer = $ok && ! str_contains ( $missing, '<img ' ) && str_contains ( $missing, '>AL</text>' )
  && str_contains ( $local, 'pad-avatar-photo-image' ) && str_contains ( $local, '&lt;Ada&gt;' )
  && str_contains ( $remote, 'referrerpolicy="no-referrer"' ) && str_contains ( $remote, 'a=1&amp;b=2' )
  ? 'unsafe sources rejected and fallbacks available' : 'FAIL';

?>
