<?php

  // The phrase was searched with ^(.*?)phrase(.*)$, whose lazy start takes one backtracking
  // step per character before the phrase: past PCRE's backtrack limit (a million steps) the
  // match failed and a phrase that stands in the text answered '' - "not there".

  $text = str_repeat ( 'lorem ipsum ', 100000 ) . 'Needle in the haystack';

  $r = json_encode ( [
    padStrExcerpt ( $text, 'needle', 6 ),
    padStrExcerpt ( str_repeat ( 'é', 1100000 ) . ' Brûlée', 'BRÛLÉE', 2 ),
    padStrExcerpt ( $text, 'haystack', 3, '(...)' ),
    padStrExcerpt ( $text, 'nowhere', 3 )
  ], JSON_UNESCAPED_UNICODE );

  unset ( $text );

?>
