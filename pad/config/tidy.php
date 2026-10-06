<?php

  // Settings for the final HTML clean-up pass, included by exits/tidy.php just before it
  // runs.
  //
  // $padTidyCcsid and $padTidyConfig are handed straight to PHP's tidy extension and apply
  // when $padTidy is on. The $padMyTidy* flags configure PAD's own lightweight fallback
  // (exits/myTidy.php), used when tidy is off but $padMyTidy is on.
  //
  // Each is a default: an application's _config/config.php, or its page, may set any of
  // them, and what it leaves unset takes the value here. They were plain assignments, and
  // since this file is read at the very end of the request they overwrote whatever the
  // application had set.
  //
  // $padMyTidySanitize names FILTER_FLAG_ constants, without the prefix, for filter_var
  // with FILTER_UNSAFE_RAW: STRIP_LOW, STRIP_HIGH, STRIP_BACKTICK, ENCODE_LOW, ENCODE_HIGH,
  // ENCODE_AMP. The step was a no-op - the names were cast to int, every one of them 0 - and
  // it is empty by default, because the two it listed would, working, have changed every
  // page: STRIP_LOW drops each character below 32, newlines and tabs among them, and
  // ENCODE_HIGH encodes byte by byte, so every UTF-8 character would come out as the
  // entities of its two to four bytes.

  $padTidyCcsid  ??= 'utf8';
  $padTidyConfig ??= [
    'output-html'         => TRUE,
    'doctype'             => 'html5',
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

  $padMyTidySanitize         ??= [];
  $padMyTidyTabToSpace       ??= TRUE;
  $padMyTidyTrim             ??= TRUE;
  $padMyTidyRemoveWhitespace ??= FALSE;
  $padMyTidyNoIndent         ??= TRUE;
  $padMyTidyNoEmptyLines     ??= TRUE;
  $padMyTidyNoNewLines       ??= FALSE;

?>
