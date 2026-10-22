<?php

  // Makes a run that grows once more, with twice the terms, when exits/short.php found what
  // the actions in front of page= left too few: every pq name back as inits/page.php kept it
  // before the build - the ones the build and the actions made since dropped - so the build
  // starts as it started the first time, from= and increment= as written, the clock of the
  // run the same; then the build, the type's exit and the actions again, as sequence/tag.php
  // and exits/exits.php run them.

  $pqPageGrowRows = $pqRows * 2;

  foreach ( preg_grep ( '/^pq(?!Store$|Entry$|Set|PageGrow)/', array_keys ( $GLOBALS ) ) as $pqPageGrowName )
    if ( ! array_key_exists ( $pqPageGrowName, $pqPageGrowState ) )
      unset ( $GLOBALS [$pqPageGrowName] );

  foreach ( $pqPageGrowState as $pqPageGrowName => $pqPageGrowValue )
    $GLOBALS [$pqPageGrowName] = $pqPageGrowValue;

  $pqRows = $pqPageGrowRows;

  include PQ . 'build/build.php';
  include PQ . 'exits/exit.php';
  include PQ . 'exits/actions.php';

?>
