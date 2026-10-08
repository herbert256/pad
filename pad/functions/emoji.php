<?php

  // Pipe function emoji: every :shortcode: of the value as its emoji - {$comment | emoji}
  // makes 'Shipped :rocket:' 'Shipped 🚀'. A shortcode it does not know stays as written,
  // as does a time like 10:30:00. The table is lib/emoji.php's, the {emoji} tag's.

  return padEmojiText ( (string) $value );

?>
