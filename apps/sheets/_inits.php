<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page, a page per example sheet and the page of every function.

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All sheets', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Sheets' ] ];

  foreach ( sheetsExamples () as $sheetsName => [ $sheetsTitle ] )
    $frameMenu [] = [ 'link' => "?$sheetsName", 'label' => $sheetsTitle, 'icon' => 'table', 'current' => $padPage == $sheetsName ];

  $frameMenu [] = [ 'head' => 'Reference' ];
  $frameMenu [] = [ 'link' => '?functions', 'label' => 'Functions', 'icon' => 'list', 'current' => $padPage == 'functions' ];

?>
