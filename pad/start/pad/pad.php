<?php

  // Runs a nested PAD pass - a second trip through the engine from inside the current one -
  // and returns what it rendered.
  //
  // start/pad/start.php saves the engine state and, for a sandboxed or reset pass, clears
  // it; inits/level.php opens a fresh level, so the pass runs at $pad+1;
  // start/pad/$padStrBld.php fills that level's base, either from a source string
  // (code.php) or by building a page (page.php); start/pad/level.php then runs the level
  // loop until that level closes again, and start/pad/end.php puts the saved state back.
  // The nested level's output, $padOut[$pad+1], is the value returned to the include.

  // A pass whose text is a template written to render once - an explicit {code} or
  // {sandbox}, as a tag or as a pipe, a mail template (lib/mail.php sets $padSectionsNext
  // for its pass) and a page build (build/build.php says so for its level) - has its
  // @start@ and @end@ taken out by occurrence/init.php. Text the engine itself runs through
  // padCode() - a _data file, a snippet, an option - is no such template, and a marker in it
  // is its own text: decided here, while $pad is still the level that asked for the pass.

  $padSectionsPass = ( ( $padSectionsNext ?? FALSE ) or in_array ( $padTag [$pad] ?? '', [ 'code', 'sandbox' ], TRUE ) );
  $padSectionsNext = FALSE;

  include PAD . 'start/pad/start.php';
  include PAD . 'inits/level.php';

  $padSectionsOnce [$pad] = $padSectionsPass;
  include PAD . "start/pad/$padStrBld.php";
  include PAD . 'start/pad/level.php';

  // An isolated pass gets its stores back as they were, the {push} stacks among them, so a
  // {stack} it printed is filled in now, from what was pushed while it ran - lib/stack.php.

  if ( $padStrBox or $padStrCln or $padStrRes )
    $padOut [$pad+1] = padStackFill ( $padOut [$pad+1] );

  // A {toc} lists the headings of the page that holds it: a page a {page} renders fills its
  // own here, before the page around it sees it - lib/toc.php.

  if ( $padStrBld == 'page' )
    $padOut [$pad+1] = padTocFill ( $padOut [$pad+1] );

  include PAD . 'start/pad/end.php';

  return $padOut [$pad+1] ;

?>
