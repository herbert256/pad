<?php

  // One string of nine thousand characters - a base64 picture in a JSON file, an HTML
  // template in a script: the rule repeated a group per character and ran out of PCRE's JIT
  // stack, and the whole text was shown without a colour. Each line: the language, then
  // whether the long string is one string token and the tokens after it are coloured.

  $long  = str_repeat ( 'A', 9000 );
  $texts = [ 'json' => [ "{\"a\": 1, \"img\": \"$long\", \"b\": true}", 'hl-str', 'hl-lit' ],
             'js'   => [ "x = `$long`; y = 'a\\'b';",               'hl-str', 'hl-str' ],
             'bash' => [ "echo \"$long\" && ls",                       'hl-str', 'hl-pun' ],
             'sql'  => [ "select '$long', 'it''s' from t",             'hl-str', 'hl-kwd' ] ];

  $lines = [];

  foreach ( $texts as $lang => [ $text, $long_kind, $after ] ) {
    $html    = padHighlightTokens ( $text, $lang );
    $lines[] = [ 'lang'  => $lang,
                 'long'  => preg_match ( '/<span class="' . $long_kind . '">[^<]{9000,}<\/span>/', $html ) ? 'one token' : 'not coloured',
                 'after' => substr_count ( $html, "class=\"$after\"" ) ];
  }

?>
