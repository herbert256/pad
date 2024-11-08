<?php
  

  // lib tidy

  $padTidyCcsid  = 'utf8'; 
  $padTidyConfig = [ 
    'output-html'         => TRUE,
    'doctype'             => 'loose',
    'wrap'                => 0,
    'indent'              => TRUE,
    'tab-size'            => 2,
    'vertical-space'      => 'no',
    'indent-spaces'       => 2,
    'replace-color'       => 'yes',
    'omit-optional-tags'  => 'yes',
    'clean'               => 'yes',
    'drop-empty-elements' => 'yes',
    'merge-spans'         => 'yes',
    'force-output'        => true,
    'show-warnings'       => FALSE,
    'merge-divs'          => 'yes'
  ];


  // myTidy
  // A basic & buggy implementation of formatting the output HTML 
  // Only used when $padTidy is FALSE

  $padMyTidySanitize         = [ 'STRIP_LOW', 'ENCODE_HIGH' ];
  $padMyTidyTabToSpace       = TRUE;
  $padMyTidyTrim             = TRUE;
  $padMyTidyRemoveWhitespace = FALSE;  
  $padMyTidyNoIndent         = TRUE;
  $padMyTidyNoEmptyLines     = TRUE;
  $padMyTidyNoNewLines       = FALSE;
 

?>