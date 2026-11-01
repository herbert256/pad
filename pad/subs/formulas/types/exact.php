<?php

  // exact - EXACT(text1, text2) is TRUE when two texts are the same, character for character and in
  // the same case - where = takes no notice of case - else FALSE. A number is compared as the text
  // it shows, TRUE and FALSE as those words, an empty cell as the empty text.
  //
  //   =EXACT("word", "word")        TRUE
  //   =EXACT("Word", "word")        FALSE
  //   =EXACT("w ord", "word")       FALSE
  //   =EXACT(B6, "0")               TRUE

  return [
    'group' => 'Text',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $one = padFormulaArgText ( $args [0], $env );
      $two = padFormulaArgText ( $args [1], $env );
      return padFormulaFirstError ( $one, $two ) ?? ( $one === $two );
    }
  ];

?>
