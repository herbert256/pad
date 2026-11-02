<?php

  // morse - a text in the international Morse code of ITU-R M.1677-1: the code written out,
  // its characters apart by a space and its words by a slash, and under it the signal as it
  // sounds - a dot one unit long, a dash three, the gaps one, three and seven units - each
  // character with its letter under it. The letters, the digits, the punctuation of the
  // recommendation (. , : ? ' - / ( ) " = + × @) and é; plain writes the code alone. For a
  // screen reader it is the text and the code in words.
  //
  //   {notation:morse 'SOS'/}
  //   {notation:morse 'Hello world', plain/}

  list ( $padMorseWords, $padMorseUnknown ) = padMorseCodes ( $padNotationText );

  if ( $padMorseUnknown and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has no Morse code for '" . padMakeSafe ( implode ( '', $padMorseUnknown ), 20 )
               . "' - the international code has the letters, the digits, é and . , : ? ' - / ( ) \" = + × @" );

  if ( ! $padMorseWords )
    return '';

  $padMorseLabel = padMakeSafe ( $padNotationText, 300 ) . ' in Morse code. ' . padMorseSpoken ( $padMorseWords );

  return padNotationWrap ( padNotationStyle ( 'morse', PAD_MORSE_COLOURS )
       . '<span class="pad-morse" role="img" aria-label="' . padNotationAttr ( $padMorseLabel ) . '">'
       . '<span class="pad-morse-code">' . padNotationAttr ( padMorseText ( $padMorseWords ) ) . '</span>'
       . ( padTagParm ( 'plain', FALSE ) ? '' : padMorseSvg ( $padMorseWords, 110 ) )
       . '</span>' );

?>
