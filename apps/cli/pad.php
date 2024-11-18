<?php
  
  $padDir  = dirname (__FILE__);
  $padHome = substr ( $padDir, 0, strpos  ( $padDir, 'apps/') - 1);
  $padApp  = substr ( $padDir,    strrpos ( $padDir, '/'    ) + 1);

  define ( 'APP', "$padHome/apps/$padApp/" );
  define ( 'PAD', "$padHome/pad/"          );
  define ( 'DAT', "$padHome/data/"         );

  include PAD . 'pad.php';

?>