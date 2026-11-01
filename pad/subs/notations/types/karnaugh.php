<?php

  // karnaugh - a Boolean function of two to four variables drawn as its Karnaugh map, the cells
  // in Gray-code order with their 1s, 0s and don't cares, the groups of a minimal sum of
  // products looped round, and the minimal expression under the map in MathML. The function is
  // its minterms with the don't cares, m(1,3,5) d(0) or Σm(...); its maxterms, M(0,2); the
  // output column of its truth table, 0110 1001; or an expression - A'B + AC, ~(A ^ B), A and
  // not C - optionally named, F = ..., F(A, B, C) = .... vars= names the variables in order,
  // A B C D for terms and tables unless given; form='pos' groups the 0s for a minimal product
  // of sums; numbers writes the number of every cell in its corner; title= is the accessible
  // name. A screen reader hears the rows of the map, its groups and the expression in words.
  //
  //   {karnaugh 'm(6,8,9,10,11,12,13,14)'/}
  //   {karnaugh numbers}F = AB + A'C + BC{/karnaugh}
  //   {karnaugh 'M(0,1,2,3,4,5,7,15)', vars='A, B, C, D', form='pos'/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padKarnaughRead = padKarnaughRead ( $padNotationText, padNotationOption ( 'vars' ) );

  if ( is_string ( $padKarnaughRead ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padKarnaughRead" );
    return '';
  }

  list ( $padKarnaughName, $padKarnaughVars, $padKarnaughCells ) = $padKarnaughRead;

  $padKarnaughSum     = ( padNotationChoice ( 'form', 'sop', [ 'sop', 'pos' ] ) == 'pos' );
  $padKarnaughWant    = $padKarnaughSum ? '0' : '1';
  $padKarnaughCount   = count ( $padKarnaughVars );
  $padKarnaughGroups  = padKarnaughCover ( padKarnaughPrimes ( $padKarnaughCells, $padKarnaughCount, $padKarnaughWant ),
                                           $padKarnaughCells, $padKarnaughCount, $padKarnaughWant );

  // What the expression says is checked against every cell before it is written.

  if ( ! padKarnaughCheck ( $padKarnaughGroups, $padKarnaughCells, $padKarnaughCount, $padKarnaughSum ) ) {
    padError ( "the " . padNotationWhat () . " found an expression that does not hold for its map" );
    return '';
  }

  $padKarnaughWords = padKarnaughWords ( $padKarnaughName, $padKarnaughGroups, $padKarnaughVars, $padKarnaughSum );
  $padKarnaughTitle = padNotationOption ( 'title' );

  if ( $padKarnaughTitle === '' )
    $padKarnaughTitle = "Karnaugh map of $padKarnaughName(" . implode ( ', ', $padKarnaughVars ) . ')';

  $padKarnaughId = padNotationId ( 'karnaugh', [ $padKarnaughVars, $padKarnaughCells, $padKarnaughSum, $padKarnaughTitle ] );

  return padNotationWrap ( padNotationStyle ( 'karnaugh', PAD_KARNAUGH_COLOURS )
       . padNotationStyle ( 'math', [ 'stroke' => [ '#c8102e', '#ff6b7d' ] ] )
       . '<span class="pad-karnaugh">'
       . padKarnaughSvg ( $padKarnaughVars, $padKarnaughCells, $padKarnaughGroups, (bool) padTagParm ( 'numbers', FALSE ), $padKarnaughSum,
                          $padKarnaughId, $padKarnaughTitle,
                          padKarnaughDescribe ( $padKarnaughName, $padKarnaughVars, $padKarnaughCells, $padKarnaughGroups, $padKarnaughSum ) )
       . padMathMl ( padKarnaughAscii ( $padKarnaughName, $padKarnaughGroups, $padKarnaughVars, $padKarnaughSum ), FALSE, "$padKarnaughWords." )
       . '</span>' );

?>
