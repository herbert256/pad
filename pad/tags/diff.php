<?php

  // {diff $old, $new} - what changed between two texts, lib/diff.php: the words taken out
  // as <del>, the words that came in as <ins>, every piece escaped. lines compares line by
  // line, in one column with the old and the new line numbers; side puts the two versions
  // next to each other, a changed line beside its counterpart. Within a changed line the
  // words that changed are marked. from= and to= name the two versions over the columns.

  if ( ! isset ( $padOpt [$pad] [2] ) and $padCheckSyntax )
    padError ( 'the diff has no second text - write {diff $old, $new}' );

  $padDiffOld  = padUnprotect ( (string) $padParm );
  $padDiffNew  = padUnprotect ( (string) ( $padOpt [$pad] [2] ?? '' ) );
  $padDiffSide = (bool) padTagParm ( 'side', FALSE );
  $padDiffFrom = padUnprotect ( (string) padTagParm ( 'from' ) );
  $padDiffTo   = padUnprotect ( (string) padTagParm ( 'to' ) );

  if ( padTagParm ( 'lines', FALSE ) or $padDiffSide )
    return padDiffLines ( $padDiffOld, $padDiffNew, $padDiffSide, $padDiffFrom, $padDiffTo );

  return padDiffWords ( $padDiffOld, $padDiffNew );

?>
