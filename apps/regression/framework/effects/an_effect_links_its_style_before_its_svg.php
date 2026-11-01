<?php

  // An effect keeps its style in an inline svg of no size, which tidy takes the <style> out
  // of without ending the paragraph around it. A <link> is no element of an svg - one inside
  // it is never loaded - so the links to the style sheets of www/assets/effects/ stand
  // before the svg, the colours inside it; in a whole page padWidgetHead moves them into the
  // <head>. Rendered here as a part of a page is, where they stay.

  $padAssetLinks = TRUE;

  $fxHtml = padUnprotect ( padCode ( '{typewriter}typed{/typewriter}' ) );

?>
