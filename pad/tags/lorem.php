<?php

  // {lorem words=50} - placeholder text, lib/lorem.php: "Lorem ipsum dolor sit amet, ..."
  // and then Latin words picked by a seeded sequence, so the text is the same on every
  // request until seed= asks for another. words= gives so many words as sentences,
  // sentences= so many sentences, paragraphs= so many <p> paragraphs - of sentences=
  // sentences each when that is given too, else four to seven. Without any of them it is
  // 50 words; fewer than five are a title, without a full stop. varied leaves the classic
  // opening out - for the second text on a page.

  $padLoremKinds = [];

  foreach ( [ 'words', 'sentences', 'paragraphs' ] as $padLoremKind ) {

    $padLoremValue = padTagParm ( $padLoremKind, NULL );

    if ( $padLoremValue === NULL )
      continue;

    if ( ! ctype_digit ( (string) $padLoremValue ) or (int) $padLoremValue < 1 or (int) $padLoremValue > 10000 ) {
      if ( $padCheckSyntax )
        padError ( "the lorem has $padLoremKind='" . padMakeSafe ( $padLoremValue, 20 ) . "' - a number from 1 to 10000" );
      $padLoremValue = 1;
    }

    $padLoremKinds [$padLoremKind] = (int) $padLoremValue;

  }

  if ( isset ( $padLoremKinds ['words'] ) and count ( $padLoremKinds ) > 1 and $padCheckSyntax )
    padError ( 'the lorem has words= beside sentences= or paragraphs= - give one length' );

  $padLoremSeed   = (string) padTagParm ( 'seed', 0 );
  $padLoremVaried = (bool) padTagParm ( 'varied', FALSE );

  if ( isset ( $padLoremKinds ['paragraphs'] ) )
    return padLorem ( 'paragraphs', $padLoremKinds ['paragraphs'], $padLoremKinds ['sentences'] ?? 0, $padLoremSeed, $padLoremVaried );

  if ( isset ( $padLoremKinds ['sentences'] ) )
    return padLorem ( 'sentences', $padLoremKinds ['sentences'], 0, $padLoremSeed, $padLoremVaried );

  return padLorem ( 'words', $padLoremKinds ['words'] ?? 50, 0, $padLoremSeed, $padLoremVaried );

?>
