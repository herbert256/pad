<?php

  // Assembles the complete page source, then hands it to the tag engine.
  //
  // Runs the build steps in order: dirs (the directory chain), libs (the _lib content),
  // base (the nested _inits.pad/@page@/_exits.pad frame) and page (the page's own PHP
  // plus its .pad). The page is dropped into the frame's @page@ hole, the whole thing
  // becomes $padBase [$pad], and occurrence/occurrence.php starts the first pass over it.

  include PAD . 'build/dirs.php';

  $padBuildLib  = include PAD . 'build/libs.php';
  $padBuildBase = include PAD . 'build/base.php';
  $padBuildPage = include PAD . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@page@', $padBuildPage, $padBuildBase );

  if ( $padInfo )
    include PAD . 'events/build.php';

  include PAD . 'occurrence/occurrence.php';

?>