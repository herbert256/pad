<?php

  function padMainSessionStart () {

    global $padInfoDir;

    padInfoFile ( "$padInfoDir/pad-entry.json", padSessionStart () );

  }


  function padMainSessionEnd () {

    global $padInfoDir;

    padInfoFile ( "$padInfoDir/pad-exit.json", padSessionEnd () );

  }


  function padMainData ( ) {

    global $padInfoDir, $padOutput;

    padInfoFile ("$padInfoDir/output.pad", $padOutput);

  }


?>