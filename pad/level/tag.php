<?php

  // Decides whether the tag is a single tag or one half of an open/close pair, and locates
  // its closing tag.
  //
  // A trailing '/' ({tag /}) forces the single form, after the opening pipe has been split
  // off. Otherwise $padOut [$pad] is scanned forward for {/<tag>, using the prefixed or
  // sequence form of the name where one applies, skipping any match that would leave the
  // pair unbalanced (padOpenCloseCountOne) or that is only the prefix of a longer name.
  // A match hands over to level/pair.php; no match leaves $padPairSet FALSE and the tag is
  // treated as single.

  $padPairSet    = FALSE;
  $padBaseSet    = '';
  $padPrmTypeSet = ( count($padWords) > 1 ) ? 'open' : 'none';

  if ( substr($padBetween, -1) == '/') {
    $padBetween = substr($padBetween, 0, -1);
    include PAD . 'level/pipes/start.php';
    include PAD . 'level/between.php';
    return;
  }

  if ( $padTypeSeq )
    $padPairTag = $padTypeSeq . ':' . $padTypeCheck;
  else
    $padPairTag = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;

  $padPos       = $padEnd [$pad];
  $padPairCheck = '';

  while ( ! in_array ( $padPairCheck, [' ', '}'] ) ) {

    $padPos = strpos($padOut [$pad] , '{/' . $padPairTag, ++$padPos);

    if ($padPos === FALSE)
      return;

    $padBaseBase = substr($padOut [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1);

    if ( padOpenCloseCountOne ( $padBaseBase, $padPairTag) )
      $padPairCheck = substr($padOut [$pad], $padPos + strlen($padPairTag) + 2, 1);

  }

  include PAD . 'level/pair.php';

?>