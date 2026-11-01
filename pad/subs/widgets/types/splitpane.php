<?php

  // {splitpane size=30}{tab 'Folders'}...{/tab}{tab 'Message'}...{/tab}{/splitpane} - two
  // panes side by side, or stacked, with a bar between them that resizes them: the bar is a
  // separator of the WAI-ARIA window splitter pattern, moved by a drag and by the arrow keys,
  // Home and End. The two {tab}s are the panes, their labels name them. size= is the first
  // pane's share in percent (50), min= and max= its limits (10 and 90); stacked puts the
  // panes one above the other, height= gives the panes their height (a CSS length - 20rem
  // when stacked), collapse lets Enter and a double click fold the first pane away and back.
  // Without scripting the first pane has the browser's own resize grip in its corner. A
  // small script (CSP nonce) moves the bar. subs/widgets/_lib/splitpane.php; the {tab}s are
  // collected as {tabs} collects them, subs/widgets/_lib/tabs.php.
  //
  // Like {live} it runs twice: first it opens the collection its {tab}s hand their content
  // to and asks for the end walk, then it builds the panes from what they handed over.

  if ( $padWalk [$pad] == 'start' ) {

    if ( ! $padPair [$pad] and $padCheckSyntax )
      padError ( "the pair {splitpane} never closes" );

    padTabsOpen ( 'splitpane' );

    $padWalk [$pad] = 'end';

    return TRUE;

  }

  $padSplitItems    = padTabsItems ( 'splitpane', $padContent );
  $padSplitMin      = padSplitpaneShare ( 'min', 10 );
  $padSplitMax      = padSplitpaneShare ( 'max', 90 );
  $padSplitSize     = padSplitpaneShare ( 'size', 50 );
  $padSplitStacked  = (bool) padTagParm ( 'stacked', FALSE );
  $padSplitCollapse = (bool) padTagParm ( 'collapse', FALSE );
  $padSplitHeight   = padSplitpaneHeight ();

  if ( $padSplitMin > $padSplitMax or $padSplitSize < $padSplitMin or $padSplitSize > $padSplitMax ) {

    if ( $padCheckSyntax )
      padError ( "the {splitpane} needs min <= size <= max - it has min $padSplitMin, size $padSplitSize and max $padSplitMax" );

    [ $padSplitMin, $padSplitSize, $padSplitMax ] = [ 10, 50, 90 ];

  }

  if ( $padSplitItems and count ( $padSplitItems ) != 2 ) {

    if ( $padCheckSyntax )
      padError ( "the {splitpane} takes two {tab}s, the panes on either side of its bar - it has " . count ( $padSplitItems ) );

    // The lenient walk keeps every pane's content: what came after the second goes into it.

    foreach ( array_slice ( $padSplitItems, 2 ) as $padSplitMore )
      $padSplitItems [1] ['content'] .= $padSplitMore ['content'];

    $padSplitItems = array_slice ( $padSplitItems, 0, 2 );

  }

  $padContent = ( count ( $padSplitItems ) == 2 )
              ? padSplitpane ( $padSplitItems, $padSplitSize, $padSplitMin, $padSplitMax, $padSplitStacked, $padSplitCollapse, $padSplitHeight )
              : implode ( '', array_column ( $padSplitItems, 'content' ) );

  return TRUE;

?>
