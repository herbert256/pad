<?php

include_once PAD . 'subs/images/avatars/_lib/avatar.php';
$ok = padKinds ( 'images/avatars' ) === [ 'geometric', 'gravatar', 'identicon', 'initials', 'mosaic', 'photo', 'pixelbot', 'rings' ];
foreach ( [ 'avatar', 'identicon', 'gravatar' ] as $tag )
  $ok = $ok && ! padTagBuiltIn ( $tag ) && ! padTagLib ( $tag );
$html = padUnprotect ( padUnescape ( padCode ( "{avatar:initials 'Ada Lovelace'/}{avatar:identicon 'ada'/}{avatar:geometric 'ada'/}{avatar:gravatar 'ada@example.org', fallback='avatar'/}{avatar:photo 'missing.jpg', alt='Ada'/}{avatar:rings 'ada'/}{avatar:mosaic 'ada'/}{avatar:pixelbot 'ada'/}" ) ) );
$answer = $ok && substr_count ( $html, '<svg ' ) === 8 ? 'eight subsystem kinds and no old tags' : 'FAIL';

?>
