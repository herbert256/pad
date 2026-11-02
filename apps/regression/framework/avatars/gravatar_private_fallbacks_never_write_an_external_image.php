<?php

include_once PAD . 'subs/avatars/_lib/gravatar.php';
$initials = padGravatar ( 'ada@example.org', 64, 'mp', 'Ada', 'avatar' );
$icon = padGravatar ( 'ada@example.org', 64, 'mp', 'Ada', 'identicon' );
$answer = ! str_contains ( $initials . $icon, '<img' ) && ! str_contains ( $initials . $icon, 'gravatar.com' )
  && str_contains ( padGravatarUrl ( ' ADA@EXAMPLE.ORG ' ), hash ( 'sha256', 'ada@example.org' ) )
  ? 'private fallbacks and normalized hash preserved' : 'FAIL';

?>
