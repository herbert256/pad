<?php

  // Applies the opening tag's pipe. $padBase [$pad] - the level's content as the tag
  // handler left it - is run through $padPipeBefore [$pad] before any occurrence is
  // rendered, which is what makes {echo $x | upper} produce a transformed value.
  //
  // A base that is the tag's answer alone is protected here, once the pipe has had the
  // real value - level/go.php leaves it to this point under $padProtectValues.

  if ( $padPipeBefore [$pad] ) {

    $padBase [$pad] = padEval ( $padPipeBefore [$pad], $padBase [$pad], TRUE );

    // The result becomes the content the level scans, so a value the pipe wrote into it -
    // {tag | @ . $v} with $v a tag or a PHP call - ran as template code, where the closing
    // pipe's result (level/pipes/after.php) and a tag's {echo} answer are kept as text. Its
    // braces travel as the &open;/&close; stand-ins, which print as themselves; the content
    // the author wrote is turned to text the same way, as an opening pipe is documented to
    // treat what it is handed - "a field written inside would be transformed and then not
    // resolve".

    if ( $padProtectValues and is_string ( $padBase [$pad] ) )
      $padBase [$pad] = str_replace ( [ '{', '}' ], [ '&open;', '&close;' ], $padBase [$pad] );

  }

  if ( $padBaseValue [$pad] )
    $padBase [$pad] = padProtect ( $padBase [$pad] );

?>
