<?php

  // Pipe function emoji: every :shortcode: of the value as its emoji - {$comment | emoji}
  // makes 'Shipped :rocket:' 'Shipped 🚀'. A shortcode it does not know stays as written,
  // as does a time like 10:30:00. The table is tags/_lib/emoji.php's, the {emoji} tag's,
  // included here as the tag includes it - when a page uses it.

  include_once PAD . 'tags/_lib/emoji.php';

  return padEmojiText ( (string) $value );

?>
