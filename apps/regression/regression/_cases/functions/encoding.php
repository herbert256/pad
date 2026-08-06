<?php

  // Escaping and encoding, where the whole point is the exact characters that come out.
  //
  // url encodes a space as + rather than %20, which is what urlencode does and what the function
  // comment describes; FUNCTIONS.md shows %20. A literal plus in the value has to come back as
  // %2B, or the two would be indistinguishable after a round trip.
  //
  // open, close and tag build the literal text of a PAD tag, so their output is parsed again as a
  // tag unless ignore follows - which is why each of them is written with it here.

  return [

    [ 'html escapes the angle brackets',
      <<<'PAD'
      {echo '<script>' | html}
      PAD,
      '&lt;script&gt;' ],

    [ 'html escapes quotes',
      <<<'PAD'
      {echo '"q"' | html}
      PAD,
      '&quot;q&quot;' ],

    [ 'html escapes the ampersand',
      <<<'PAD'
      {echo 'a&b' | html}
      PAD,
      'a&amp;b' ],

    [ 'sanitize',
      <<<'PAD'
      {echo '<b>x</b>' | sanitize}
      PAD,
      '&lt;b&gt;x&lt;/b&gt;' ],

    [ 'url encodes a space as plus',
      <<<'PAD'
      {echo 'hello world' | url}
      PAD,
      'hello+world' ],

    [ 'url encodes a slash',
      <<<'PAD'
      {echo 'a/b' | url}
      PAD,
      'a%2Fb' ],

    [ 'url encodes a literal plus',
      <<<'PAD'
      {echo 'a+b' | url}
      PAD,
      'a%2Bb' ],

    [ 'slashes',
      <<<'PAD'
      {echo "it's here" | slashes}
      PAD,
      'it\\\'s here' ],

    [ 'slashes and stripslashes undo each other',
      <<<'PAD'
      {echo 'a' | slashes | stripslashes}
      PAD,
      'a' ],

    [ 'bold',
      <<<'PAD'
      {echo 'important' | bold}
      PAD,
      '<b>important</b>' ],

    [ 'nbsp',
      <<<'PAD'
      {echo 'hello world' | nbsp}
      PAD,
      'hello&nbsp;world' ],

    [ 'encodeHigh',
      <<<'PAD'
      {echo 'café' | encodeHigh}
      PAD,
      'caf&#195;&#169;' ],

    [ 'open builds a tag, ignore keeps it from being one',
      <<<'PAD'
      {echo 'items' | open | ignore}
      PAD,
      '{items}' ],

    [ 'close builds the closing tag',
      <<<'PAD'
      {echo 'items' | close | ignore}
      PAD,
      '{/items}' ],

    [ 'tag is the same as open',
      <<<'PAD'
      {echo 'items' | tag | ignore}
      PAD,
      '{items}' ],

  ];

?>
