<?php

  // Splits the level's template text on an @else@ marker belonging to this tag.
  //
  // Text before the marker stays in $padBase [$pad] as the true branch; text after it goes
  // to $padFalse, which level/base.php uses when the tag turns out null or empty, and
  // $padFalseSplit says the cut was made - for a tag that takes its content as text. A marker
  // is only this level's if every open/close tag pair before it - the pairs found in the
  // text plus this tag itself - is balanced, so @else@ inside a nested pair is skipped.

  // Most levels have no @else@ at all, and the pair list is a scan of the whole text: it
  // is only made when there is a marker to place.

  if ( ! str_contains ( $padBase [$pad], '@else@' ) )
    return;

  // What {ignore} wraps is text, and so is the content of a level carrying the ignore option
  // - the engine stops seeing PAD markup in it - and the content of {data}, which is read as
  // data: an @else@ there is part of that text, not a branch of the tag. Cut off, it was
  // gone - {ignore}a@else@b{/ignore} printed a, {markdown ignore} lost the rest of a sentence
  // that showed the marker, and {data 'd'}["a@else@b"]{/data} failed as JSON. A {highlight}
  // sample is code shown as it stands, left whole here too - it was put back together by
  // tags/highlight.php, and a second @else@ in it would read as one too many.

  if ( ( $padType [$pad] == 'pad' and in_array ( $padTag [$pad], [ 'ignore', 'data', 'highlight' ] ) )
       or isset ( $padPrm [$pad] ['ignore'] ) )
    return;

  // The marker is looked for in padElseText (lib/content.php), where one written inside the
  // braces of a tag - a quoted parameter, {echo 'x@else@y'} - is no marker of the level, and
  // what an {ignore} or a {highlight} sample shows is counted as the text it is.

  $padSplitText = padElseText ( $padBase [$pad] );

  $padOpenClose = padOpenCloseList ( $padSplitText ) ;

  if ( $padGiven [$pad] )
    $padOpenClose [ $padType [$pad] . ':' . $padTag [$pad] ] = TRUE;
  else
    $padOpenClose [ $padTag [$pad] ] = TRUE;

  $padPos = strpos ( $padSplitText, '@else@');

  while ( $padPos !== FALSE) {

    if  ( padOpenCloseCount ( substr ( $padSplitText, 0, $padPos ), $padOpenClose) ) {
      if ( $padCheckSyntax )
        padElseSecond ( $padSplitText, $padPos, $padOpenClose );
      $padFalse = substr ( $padBase [$pad], $padPos+6  );
      $padBase  [$pad] = substr ( $padBase [$pad], 0, $padPos );
      $padFalseSplit = TRUE;
      if ( $padInfo )
        include PAD . 'events/else.php';
      return;
    }

    $padPos = strpos ( $padSplitText, '@else@', $padPos+1);

  }

?>
