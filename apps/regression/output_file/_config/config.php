<?php

  $padCommon = FALSE;

  // Only the payload goes through the file writer - the index has a verdict to show, and
  // a page written to disk shows nothing.

  $padOutputType = isset ( $_REQUEST ['payload'] ) ? 'file' : 'web';

  $padFileDir       = 'regression_output_file';
  $padFileName      = 'payload';
  $padFileExtension = 'html';
  $padFileNextPage  = 'done';

?>