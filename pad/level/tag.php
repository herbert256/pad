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

  // The slash may stand at the very end, behind a pipe - {echo $x | upper /} - or in front
  // of the pipe - {echo $x /| upper}. Only the second was looked for, and it lost its pipe:
  // splitting the pipe off the cut text again blanked the one already split off. The first
  // left the slash in the pipe, where it was evaluated as a division.

  if ( substr ( $padBetweenOrg, -1 ) == '/' or substr ( $padBetween, -1 ) == '/' ) {

    // What was written, slash and all. level/pipes/start.php takes $padBetweenOrg from
    // $padBetween, which by then has had the slash cut off, so the text level/no.php puts
    // back for a name nothing claims would come out a character short of what it was given -
    // {nosuchtag/} landing in the page as {nosuchtag}.

    $padTagSlashOrg = $padBetweenOrg;

    if ( substr ( $padBetweenOrg, -1 ) == '/' ) {
      $padBetween = substr ( $padBetweenOrg, 0, -1 );
      include PAD . 'level/pipes/start.php';
    } else
      $padBetween = substr ( $padBetween, 0, -1 );

    include PAD . 'level/between.php';

    // The name is resolved again, because level/level.php resolved it before the slash came
    // off. With options that made no difference - the slash sat at the end of the options and
    // the first word was already the name - but with none at all the slash was part of the
    // name, {box/} was looked up as a tag called "box/", and nothing claimed it.

    include PAD . 'level/type.php';

    $padBetweenOrg = $padTagSlashOrg;

    return;

  }

  if ( $padTypeSeq )
    $padPairTag = $padTypeSeq . ':' . $padTypeCheck;
  else
    $padPairTag = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;

  $padPos       = $padEnd [$pad];
  $padPairCheck = '';

  // With no {tag of the name between the tag and a closer, there is nothing in between to
  // balance: no opener, and no closer either - every closer passed over before it was a
  // longer name's, {/iffy} for if, which neither count reads. That text was cut out and
  // counted for every closer of every pair, most of them with no opener in between.

  $padPairOpen = strpos ( $padOut [$pad], '{' . $padPairTag, $padEnd [$pad] + 1 );

  // A closer is read as an opener is: its name followed by white space, the }, or a pipe
  // written without a space - {/items|upper}, {/items and a line break. Only a space or the }
  // made a closer, and the pair fell apart: the opener was taken single and the closer was
  // left in the page.

  // What an {ignore} wraps and a {highlight} sample - a text pair, padTextBlank in
  // lib/template.php - is text: a closer of the pair's name inside one is no closer, and an
  // opener or closer of its name there is not counted. {xs}... {ignore}{/xs}{/ignore} ...{/xs}
  // closed the loop at the sample, and {if $a}... {ignore}{if $x}{/ignore} ...{/if} never
  // closed. A pair that is a text pair itself counts its own name only, as before - its
  // content is all text.

  $padPairTexts = ! ( in_array ( $padTypeCheck, padTextPairs, TRUE ) or padTextOption ( $padBetween ) );

  while ( $padPairCheck === '' or ! ( $padPairCheck == '}' or $padPairCheck == '|' or ctype_space ( $padPairCheck ) ) ) {

    $padPos = strpos($padOut [$pad] , '{/' . $padPairTag, ++$padPos);

    if ($padPos === FALSE)
      return;

    $padPairText = $padPairTexts ? padTextBlank ( $padOut [$pad], $padEnd [$pad] + 1, $padPos ) : NULL;

    if ( $padPairText === FALSE )
      continue;

    if ( $padPairOpen === FALSE or $padPairOpen > $padPos
         or padOpenCloseCountOne ( $padPairText ?? substr ( $padOut [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1 ), $padPairTag ) )
      $padPairCheck = substr($padOut [$pad], $padPos + strlen($padPairTag) + 2, 1);

  }

  $padBaseBase = substr($padOut [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1);

  include PAD . 'level/pair.php';

?>
