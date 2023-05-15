<?php

  set_time_limit(10);

  $padBigFiles = [];

  $padBigNot = ['page/redirect', 'page/restart', 'dir'];

  foreach ( padPages () as $padBigItem ) {

    if ( ! in_array($padBigItem, $padBigNot) ) {

      $padBigFiles [$padBigItem] ['padBigItem'] = $padBigItem;
 
      if ($big == 'example' ) {
        $padBigFile = padApp . "pages/$padBigItem.html";
        if ( substr($padBigItem, -5) == 'index' )  
          $padBigFiles [$padBigItem] ['padBigOnlyResult'] = ',onlyResult';
        elseif ( padExists ($padBigFile) and strpos(file_get_contents($padBigFile), '{demo}') !== false )  
          $padBigFiles [$padBigItem] ['padBigOnlyResult'] = ',onlyResult';
        else
          $padBigFiles [$padBigItem] ['padBigOnlyResult'] = '';
      }

    }

  }
 
?>