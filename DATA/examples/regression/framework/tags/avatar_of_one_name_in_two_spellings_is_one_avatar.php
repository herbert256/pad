<?php

  // One name in two Unicode spellings - Åsa Öberg composed, or A and O with their marks
  // apart, as macOS file names and copy-paste give it - is one avatar: the same initials
  // and the same colour, where the marks were dropped and the colour changed. A handle's
  // leading @ is no e-mail address: @herbert was a '?'.

  include_once PAD . 'tags/_lib/avatar.php';

  $nfc = "\u{00C5}sa \u{00D6}berg";
  $nfd = "A\u{030A}sa O\u{0308}berg";

  $same = ( padAvatarInitials ( $nfc ) === padAvatarInitials ( $nfd ) and padAvatarSlot ( $nfc ) === padAvatarSlot ( $nfd ) ) ? 'one avatar' : 'two avatars';
  $first = padAvatarInitials ( $nfd );
  $handles = padAvatarInitials ( '@herbert' ) . ' ' . padAvatarInitials ( '@jebbink herbert' ) . ' ' . padAvatarInitials ( 'herbert.jebbink@example.com' );

?>
