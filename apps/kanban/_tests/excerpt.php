<?php

  // A card's excerpt: its details without Markdown marks, cut at a word near 90 characters.

  $short = kanbanExcerpt ( 'Run `pad pack shop` - *now*.' );
  $long  = kanbanExcerpt ( str_repeat ( 'move cards across ', 10 ) );

?>
