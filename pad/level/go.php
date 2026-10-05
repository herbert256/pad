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

  if ( padSingleValue ( $padTagResult ) ) {

    $padTagIsValue = $padProtectValues && ! padTagAnswersSource ();

    if ( $padTagIsValue and $padContent === '' and $padTagContent === '' and ! padTagParm ( 'content' ) ) {

      $padContent          .= $padTagResult;
      $padBaseValue [$pad]  = TRUE;

    } else

      $padTagContent .= $padTagIsValue ? padProtect ( $padTagResult ) : $padTagResult;

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

  if ( padTagParm ('content') ) {
    $padContentData = include PAD . 'options/content.php';
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad] );
  }

?>