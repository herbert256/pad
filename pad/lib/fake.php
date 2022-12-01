<?php


  function padBuild ( $padFakePage,  $padFakeMode='include', $padFakeMerge='content' ) {

    include PAD . 'fake/inits.php'; 

    $page          = $padFakePage;
    $padBuildMode  = $padFakeMode;
    $padBuildMerge = $padFakeMerge;

    include PAD . 'build/build.php'; 

    $padHtml [$pad] = $padBase [$pad];    

    return include PAD . 'fake/exits.php'; 
 
  }


  function padFake ( $padFakeContent ) {

    include PAD . 'fake/inits.php'; 

    $padHtml [$pad] = $padFakeContent;    

    return include PAD . 'fake/exits.php'; 

  }


  padFakeFunction ($tag, $parms) {

    return padFake ( "{$tag $parms}" );

  }

?>