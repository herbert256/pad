<?php

  function padMainSessionStart () {

    global $padInfoDir;

    padInfoPut ( "$padInfoDir/pad-entry.json", padSessionStart () );

  }


  function padMainSessionEnd () {

    global $padInfoDir;

    padInfoPut ( "$padInfoDir/pad-exit.json", padSessionEnd () );

  }


  function padMainData ( ) {

    global $padInfoDir, $padOutput;

    padInfoPut ("$padInfoDir/output.pad", $padOutput);

  }


?>