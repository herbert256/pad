<?php

  // The validators answer for the whole text: a $ without D in a pattern also matches before
  // a newline that ends the text.

  $validNewline = implode ( ' ', [
    padValidID  ( 'abcdefgh' )   ? 'id'   : '-',
    padValidID  ( "abcdefgh\n" ) ? 'id'   : '-',
    padValidVar ( 'abc' )        ? 'var'  : '-',
    padValidVar ( "abc\n" )      ? 'var'  : '-',
    padValid    ( "abc\n" )      ? 'name' : '-',
    padValidTag ( "abc\n" )      ? 'tag'  : '-',
    padAtValid  ( "abc\n" )      ? 'at'   : '-'
  ] );

?>
