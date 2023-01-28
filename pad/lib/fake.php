<?php


  function padBuild ( $padFakePage,  $padFakeMode='include', $padFakeMerge='content' ) {

    include PAD . 'pad/fake/inits.php'; 

    $page          = $padFakePage;
    $padBuildMode  = $padFakeMode;
    $padBuildMerge = $padFakeMerge;

    include PAD . 'pad/build/build.php'; 

    $padHtml [$pad] = $padBase [$pad];    

    return include PAD . 'pad/fake/exits.php'; 
 
  }


  function padFake ( $padFakeContent ) {

    include PAD . 'pad/fake/inits.php'; 

    $padHtml [$pad] = $padFakeContent;    

    return include PAD . 'pad/fake/exits.php'; 

  }


  function padFakeFunction ($tag, $parms) {

    return padFake ( '{' . $tag . ' ' . $parms . '}' );

  }


?>