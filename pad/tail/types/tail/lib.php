<?php

  function padTailSessionStart () {

    global $padTailDir;

    padTailPut ( "$padTailDir/pad-entry.json", padSessionStart () );

  }


  function padTailSessionEnd () {

    global $padTailDir;

    padTailPut ( "$padTailDir/pad-exit.json", padSessionEnd () );

  }


  function padTailData ( ) {

    global $padTailDir, $padOutput;

    padTailPut ("$padTailDir/output.pad", $padOutput);

  }


?>