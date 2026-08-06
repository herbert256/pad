<?php

  // The three listings the pages are built from: the example pages of a directory, the
  // sequence types and the actions.
  //
  // All three read a directory, so all three skip the entries an editor or the operating
  // system leaves behind - anything starting with a dot. A .DS_Store beside the types was
  // otherwise listed as a sequence type of its own, and the catalogue asked the engine to
  // generate it.

  function sequenceDir ( $dir )  {

    $out = [];

    foreach ( padFiles ( $dir ) as $file ) {

      if ( $file [0] == '.' )
        continue;

      $key = str_replace( '.pad', '', str_replace( '.php', '', $file ) );
      $out [$key] = $key;

    }

    return array_values ( $out );

  }


  function types () {

    $out = [];

    foreach ( padFiles ( PAD . 'sequence/types' ) as $file ) {

      if ( $file [0] == '.' )
        continue;

      if ( ! is_dir ( PAD . "sequence/types/$file" ) )
        continue;

      $out [] = $file;

    }

    return $out;

  }


  function actions () {

    $out = [];

    foreach ( padFiles ( PAD . 'sequence/actions/types' ) as $file ) {

      if ( $file [0] == '.' )
        continue;

      $out [] = str_replace ( '.php', '', $file );

    }

    return $out;

  }


?>