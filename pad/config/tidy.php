<?php

  // Settings for the final HTML clean-up pass, included by exits/tidy.php just before it
  // runs.
  //
  // $padTidyCcsid and $padTidyConfig are handed straight to PHP's tidy extension and apply
  // when $padTidy is on. The $padMyTidy* flags configure PAD's own lightweight fallback
  // (exits/myTidy.php), used when tidy is off but $padMyTidy is on.

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

  $padMyTidySanitize         = [ 'STRIP_LOW', 'ENCODE_HIGH' ];
  $padMyTidyTabToSpace       = TRUE;
  $padMyTidyTrim             = TRUE;
  $padMyTidyRemoveWhitespace = FALSE;
  $padMyTidyNoIndent         = TRUE;
  $padMyTidyNoEmptyLines     = TRUE;
  $padMyTidyNoNewLines       = FALSE;

?>