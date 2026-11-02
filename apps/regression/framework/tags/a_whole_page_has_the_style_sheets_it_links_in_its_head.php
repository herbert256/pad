<?php

  // The style sheets of www/assets/ a page links go into its <head>, each once, as tidy moves
  // a <style> there: a <link> in a paragraph ended the paragraph under tidy - "Hello" and
  // "again" on lines of their own - and one in the inline svg an effect keeps its style in
  // was never loaded. The link of a {widget:shadow} root stays inside it: the page's style does not
  // reach the root.

  $padAssetLinks = TRUE;

?>
