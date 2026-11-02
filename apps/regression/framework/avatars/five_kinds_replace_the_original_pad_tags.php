<?php

include_once PAD . 'subs/avatars/_lib/avatar.php';
$ok = padKinds ( 'avatars' ) === [ 'geometric', 'gravatar', 'identicon', 'initials', 'photo' ];
foreach ( [ 'avatar', 'identicon', 'gravatar' ] as $tag )
  $ok = $ok && ! padTagBuiltIn ( $tag ) && ! padTagLib ( $tag );
$html = padUnprotect ( padUnescape ( padCode ( "{avatar:initials 'Ada Lovelace'/}{avatar:identicon 'ada'/}{avatar:geometric 'ada'/}{avatar:gravatar 'ada@example.org', fallback='avatar'/}{avatar:photo 'missing.jpg', alt='Ada'/}" ) ) );
$answer = $ok && substr_count ( $html, '<svg ' ) === 5 ? 'five subsystem kinds and no old tags' : 'FAIL';

?>
