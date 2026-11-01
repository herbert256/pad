<?php

  // proper - PROPER(text) gives every word a capital and the rest small letters - a word being a
  // run of letters, so after an apostrophe a new one starts, as in Excel: it's is It'S.
  //
  //   =PROPER("hello wORLD")        Hello World
  //   =PROPER("it's 2-way")         It'S 2-Way

  return [
    'group' => 'Text',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaArgText ( $args [0], $env );
      if ( padFormulaIsError ( $text ) )
        return $text;
      return preg_replace_callback ( '/\p{L}+/u', fn ( $word ) => mb_strtoupper ( mb_substr ( $word [0], 0, 1 ) ) . mb_strtolower ( mb_substr ( $word [0], 1 ) ), $text );
    }
  ];

?>
