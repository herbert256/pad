<?php

  // {pad:diff $old, $new/} - what changed between two texts, tags/_lib/diff.php: the words taken out
  // as <del>, the words that came in as <ins>, every piece escaped. lines compares line by
  // line, in one column with the old and the new line numbers; side puts the two versions
  // next to each other, a changed line beside its counterpart. Within a changed line the
  // words that changed are marked. from= and to= name the two versions over the columns.

  if ( ! isset ( $padOpt [$pad] [2] ) and $padCheckSyntax )
    padError ( 'the diff has no second text - write {pad:diff $old, $new/}' );

  $padDiffKind = (string) padTagParm ( 'kind', '' );
  if ( $padDiffKind !== '' and ! padKind ( 'diffs', $padDiffKind ) ) {
    if ( $padCheckSyntax ) padError ( 'Unknown diff kind' );
    return '';
  }
  if ( in_array ( $padDiffKind, [ 'json', 'rows' ], TRUE ) ) {
    try {
      $padDiffBefore = is_string ( $padParm ) ? padUnprotect ( $padParm ) : $padParm;
      $padDiffAfter = $padOpt [$pad] [2] ?? NULL;
      if ( is_string ( $padDiffAfter ) ) $padDiffAfter = padUnprotect ( $padDiffAfter );
      return padDiffRender ( padDiffCompare ( $padDiffKind, $padDiffBefore, $padDiffAfter, [ 'key' => padTagParm ( 'key' ) ] ),
        (bool) padTagParm ( 'side', FALSE ), padUnprotect ( (string) padTagParm ( 'from' ) ), padUnprotect ( (string) padTagParm ( 'to' ) ) );
    } catch ( InvalidArgumentException | LengthException | JsonException $error ) {
      if ( $padCheckSyntax ) padError ( $error->getMessage () );
      return '';
    }
  }

  $padDiffOld  = padUnprotect ( (string) $padParm );
  $padDiffNew  = padUnprotect ( (string) ( $padOpt [$pad] [2] ?? '' ) );
  $padDiffSide = (bool) padTagParm ( 'side', FALSE );
  $padDiffFrom = padUnprotect ( (string) padTagParm ( 'from' ) );
  $padDiffTo   = padUnprotect ( (string) padTagParm ( 'to' ) );

  if ( $padDiffKind === 'lines' or padTagParm ( 'lines', FALSE ) or $padDiffSide )
    return padDiffLines ( $padDiffOld, $padDiffNew, $padDiffSide, $padDiffFrom, $padDiffTo );

  return padDiffWords ( $padDiffOld, $padDiffNew );

?>
