<?php

  // The harvest, at home in develop: the one crawl left walks every page padAppsList()
  // names and gathers what the develop-owned stores hold - DATA/reference through
  // &padReference, which makes each page record what it used as it renders, and
  // DATA/examples through &padExamples, stored here from the sources beside the tidied
  // render. The suites assert those stores; develop gathers them.
  //
  function getHarvest ( $extra ) {

    foreach ( padAppsList () as $one ) {

      set_time_limit ( 60 );

      getHarvestPage ( $one ['app'], $one ['item'], $extra );

    }

  }


  // One page of the harvest: fetch it with the extras and store what the examples
  // application shows. The source is read for the markers that say a page is no example -
  // a body that is mostly another page's, a demo, an {ajax} shell - and a page with no
  // template keeps its markers in its .php, so all three halves are read.

  function getHarvestPage ( $app, $item, $extra ) {

    global $padHost;

    // An index is fetched full, everything else bare - the same rule the suites apply.

    $include = ( $item != 'index' ) ? '&padInclude' : '';

    $curl = padCurl ( "$padHost$app/?$item$include$extra" );

    if ( ! str_contains ( $extra, '&padExamples' ) or ! str_starts_with ( $curl ['result'], '2' ) )
      return;

    $source = padFileGet ( APPS . "$app/$item.pad"  )
            . padFileGet ( APPS . "$app/$item.html" )
            . padFileGet ( APPS . "$app/$item.php"  );

    if ( str_contains ( $source, '{page'    ) ) return;
    if ( str_contains ( $source, '{example' ) ) return;
    if ( str_contains ( $source, '{ajax'    ) ) return;
    if ( str_contains ( $source, '{table'   ) ) return;
    if ( str_contains ( $source, '{demo'    ) ) return;

    if ( file_exists ( APPS . "$app/$item.php" ) )
      padFilePut ( "examples/$app/$item.php",  padFileGet ( APPS . "$app/$item.php" ) );

    if ( file_exists ( APPS . "$app/$item.pad" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.pad" ) );
    elseif ( file_exists ( APPS . "$app/$item.html" ) )
      padFilePut ( "examples/$app/$item.pad",  padFileGet ( APPS . "$app/$item.html" ) );

    padFilePut ( "examples/$app/$item.html", padTidySmall ( $curl ['data'], TRUE ) );

  }

?>
