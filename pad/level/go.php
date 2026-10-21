<?php

  // Runs the tag itself: includes types/<type>.php and collects what it produces.
  //
  // Reached through try/try.php ($padTry = 'level/go') from level/start.php and
  // walk/next.php. The handler sees $padParm (first parameter), $padContent (the text
  // between the tags) and $padGetName; its return value lands in $padTagResult and anything
  // it echoes in $padTagContent. Afterwards level/flags.php turns the result into this
  // level's hit/null/else/array flags, a leading '?' in the option text runs
  // level/ternary.php, and the dump and content= options are applied.

  $padParm       = $padOpt [$pad] [1] ?? '';
  $padContent    = $padBase [$pad];
  $padTagContent = '';

  // A custom tag - one whose template takes the content in at @content@ - first has the
  // slot fills taken out of its content, kept for this use of the tag: lib/slot.php.

  if ( ! isset ( $padSlot [$pad] ) and padSlotType ( $padType [$pad] ) )
    $padSlot [$pad] = [ 'fills' => padSlotTake ( $padContent ), 'declared' => NULL ];

  // content= on a tag that cuts its own content at its branches - an {if}, a {case}, the
  // condition tags (padElseOwners) - is merged in before the tag runs. Merged after it, as
  // for any other tag, its {else}, {elseif} and {when}s were never seen: "there is no tag
  // named 'else'", "the {case} has no {when}", or the else branch lost in silence. An
  // @else@ half of the stored text goes to the level's own, as padContentMerge puts it.

  $padContentFirst = ( $padType [$pad] == 'pad' and in_array ( $padTag [$pad], padElseOwners, TRUE ) and padTagParm ( 'content' ) );

  if ( $padContentFirst ) {
    padContentElse ( include PAD . 'options/content.php', $padContentTrue, $padContentFalse );
    $padContent = padContentBranches ( $padContent, $padContentTrue, $padTag [$pad] == 'case' );
    if ( $padContentFalse !== '' )
      $padFalse = padContentSet ( $padFalse, $padContentFalse );
  }

  ob_start();
  $padGetName     = $padTag [$pad];
  $padTagResult   = include PAD . "types/" . $padType [$pad] . ".php";
  $padTagContent .= ob_get_clean();

  if ( $padNextPadLevel )
    return;

  // What a tag answers is a value - {echo $x}, a field, a PHP function's result, a fetched
  // body - and under $padProtectValues it joins the level as text, since the level goes on
  // to scan it. Template text a tag produces travels as $padTagContent, which is untouched.
  // Three kinds answer with source by design: an _include/ snippet, a stored {content}
  // block, and a _common include.
  //
  // When the answer will be the level's whole base - no content between the tags, nothing
  // printed, no content= - the protecting waits for level/pipes/before.php, so the tag's
  // own pipe works on the real value: {echo '"q"' | html} has quotes to encode. The value
  // then becomes the content as it is, not through padContentMerge, which would obey an
  // @else@ or @content@ inside it. Otherwise it is protected here, and the pipe transforms
  // the template text around it as it always did.

  // The {tag ? true : false} form chooses by the truth of the value the tag answers, as
  // {echo $qty ? 'some' : 'none'} does - '', '0', 0 and FALSE take the false side - and the
  // value itself is no part of the answer (level/ternary.php): every value was made TRUE and
  // written in front of the side, {qty ? some : none} with qty 0 printed 0some. A tag that
  // printed something, or has a template of its own, is a hit whatever it answers, as below:
  // {greet ? yes : no} over an application tag with a template whose PHP ends in return ''
  // took the false side and dropped the template, where {greet}yes@else@no{/greet} showed both.

  if ( padSingleValue ( $padTagResult ) and str_starts_with ( $padOpt [$pad] [0], '?' ) )

    $padTagResult = ( (bool) $padTagResult or $padTagContent !== '' );

  elseif ( padSingleValue ( $padTagResult ) ) {

    $padTagIsValue = $padProtectValues && ! padTagAnswersSource ();

    if ( $padTagIsValue and $padContent === '' and $padTagContent === '' and ! padTagParm ( 'content' ) ) {

      $padContent          .= $padTagResult;
      $padBaseValue [$pad]  = TRUE;

    } else

      $padTagContent .= $padTagIsValue ? padProtect ( $padTagResult ) : $padTagResult;

    // An answer of '' is a tag that produced nothing, as FALSE is: level/flags.php takes it
    // for else, so the @else@ half, else=, notOk= and demand see it. Turned into TRUE with
    // every other value, {e}T@else@F{/e} over an empty $e showed T and {e demand} passed.
    // A tag that printed something, or has a template of its own, did produce something
    // whatever it answers: an application tag whose PHP ends in return '' - keeping the 1
    // of a bare include out of the page - lost its template, and one that echoed its output.

    if ( $padTagResult !== '' or $padTagContent !== '' )
      $padTagResult = TRUE;

  }

  include PAD . 'level/flags.php';

  if ( str_starts_with ( $padOpt [$pad] [0], '?' ) )
    include PAD . 'level/ternary.php';

  if ( $padInfo )
    include PAD . 'events/type.php';

  if ( $padInfo )
    include PAD . 'events/go.php';

  if ( padTagParm ('dump') )
    include PAD . 'options/dump.php';

  // Tested against '' rather than for truth: a tag whose whole output is the single character
  // 0 has a falsy $padTagContent in PHP, and testing it for truth dropped that output on the
  // floor - {echo 0} printed nothing at all, as did any count, total or difference that came
  // out zero.

  if ( $padTagContent !== '' )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad] );

  if ( padTagParm ('content') and ! $padContentFirst ) {
    $padContentData = include PAD . 'options/content.php';
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad] );
  }

?>
