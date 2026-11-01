<?php

  // braille - a text in braille: Unified English Braille, grade 1, as the characters of
  // Unicode's braille block, the text itself the accessible name. The capital sign, the
  // capitals word indicator, the numeric indicator and the punctuation of UEB are written as
  // UEB writes them; accented letters take their modifier. cells draws the cells as raised
  // dots instead, an SVG with the character each cell stands for under it - cells=12 breaks
  // the lines at 12 cells (24 unless given).
  //
  //   {braille 'Hello, World!'/}
  //   {braille 'Room 101', cells/}

  $padBrailleCells = padTagParm ( 'cells', FALSE );

  list ( $padBrailleList, $padBrailleUnknown ) = padBrailleCells ( $padNotationText );

  if ( $padBrailleUnknown and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has no braille for '" . padMakeSafe ( implode ( '', $padBrailleUnknown ), 20 )
               . "' - it writes letters, digits, accented letters and the punctuation of Unified English Braille" );

  if ( ! $padBrailleList )
    return '';

  $padBrailleLabel = padMakeSafe ( $padNotationText, 500 );

  if ( $padBrailleCells === FALSE )
    return padNotationWrap ( padNotationStyle ( 'braille', PAD_BRAILLE_COLOURS )
         . '<span class="pad-braille" role="img" aria-label="' . padNotationAttr ( $padBrailleLabel ) . '" title="' . padNotationAttr ( $padBrailleLabel ) . '">'
         . implode ( '', array_map ( fn ( $cell ) => $cell [0] === '' ? ' ' : padBrailleChar ( $cell [0] ), $padBrailleList ) )
         . '</span>' );

  $padBrailleAcross = ( $padBrailleCells === TRUE ) ? 24 : padNotationInt ( 'cells', 24, 4, 80 );

  return padNotationWrap ( padNotationStyle ( 'braille', PAD_BRAILLE_COLOURS )
       . padBrailleDots ( $padBrailleList, $padBrailleAcross, padNotationId ( 'braille', [ $padNotationText, $padBrailleAcross ] ),
                          "$padBrailleLabel, in braille" ) );

?>
