<?php

  // {breadcrumb/} - where the page stands: a <nav> with an ordered list of links from the
  // application's home to the page, the last one the page itself (aria-current). Alone the
  // trail is the page's path, every directory a step; a pair takes it from its lines - a
  // label, or a label and a link after a bar, Kettles | ?products/kettles - and data= from
  // the rows of a store, their label and link fields. home= is the first step's text
  // ('Home'), label= names the trail for a screen reader ('Breadcrumb'). No script.
  // subs/widgets/_lib/breadcrumb.php.
  //
  // The pair's lines are template, rendered first: like {live}, the pair asks for the end
  // walk and reads what rendered.

  if ( $padPair [$pad] and $padWalk [$pad] == 'start' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padCrumbItems = padBreadcrumbItems ( $padPair [$pad], $padPair [$pad] ? $padContent : '' );

  $padCrumbHtml = $padCrumbItems ? padBreadcrumb ( $padCrumbItems, padWidgetText ( 'label', 'Breadcrumb' ) ?: 'Breadcrumb' ) : '';

  if ( ! $padPair [$pad] )
    return $padCrumbHtml;

  $padContent = $padCrumbHtml;

  return TRUE;

?>
