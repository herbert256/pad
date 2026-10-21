<?php

  // A character is what a reader sees as one: a flag is two code points, a family emoji
  // three joined by two more, an e with a combining accent two - counted in code points
  // they were cut in half, half a flag left and the accent cut off its letter.

  $flags  = "\u{1F1F3}\u{1F1F1}\u{1F1E7}\u{1F1EA}";
  $family = "\u{1F468}\u{200D}\u{1F469}\u{200D}\u{1F467}";
  $etude  = "e\u{0301}tude";

  $r = json_encode ( [
    'flag'    => padStrLimit   ( $flags, 1 ) === "\u{1F1F3}\u{1F1F1}...",
    'family'  => padStrLimit   ( "$family family", 2 ) === "$family...",
    'accent'  => padStrLimit   ( $etude, 1 ) === "e\u{0301}...",
    'fits'    => padStrLimit   ( $etude, 5 ) === $etude,
    'mask'    => padStrMask    ( $flags . 'x', '*', 1 ) === "\u{1F1F3}\u{1F1F1}**",
    'maskEnd' => padStrMask    ( "ab$family", '*', -1 ) === 'ab*',
    'excerpt' => padStrExcerpt ( "$flags and $etude", 'and', 2 ) === "...\u{1F1E7}\u{1F1EA} and e\u{0301}...",
    'plain'   => padStrLimit   ( 'The quick brown fox', 10 )
  ] );

?>
