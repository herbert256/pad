<?php

  if ( isset ( $padContentStore [ $padTag [$pad] ] ) )
    return $padContentStore [ $padTag [$pad] ];

  if ( padContentCheck ( $padTag [$pad]  ) ) {

    $padT = $padTag [$pad];

    foreach ( padDirs () as $padK => $padV ) {

      $padF = APP2 . $padV . "_content/$padT.pad";

      if ( file_exists ($padF) )  {

        $padD = padFileGetContents ($padF);

        padBeforeAfter2 ( $padD, $padB, $padA, '@content@' );
        if ( $padB and $padA )
          return $padB . $padContent . $padA;

        padBeforeAfter2 ( $padContent, $padB, $padA, '@content@' );
        if ( $padB and $padA )
          return $padB . $padD . $padA;

        $padM = padTagParm ( 'merge', 'replace' );

        if     ( $padM == 'bottom'  ) return $padContent . $padD;
        elseif ( $padM == 'top'     ) return $padD . $padContent;
        elseif ( $padM == 'replace' ) return $padD;

      }

    }

  }

?>