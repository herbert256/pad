<?php

  // Reads the four options that decide how isolated a nested pass is, then starts it.
  //
  //   sandbox   hide the outer state completely and restore it afterwards
  //   reset     clear the level arrays and stores, but leave the app variables
  //   clean     let the pass see the outer state, but undo whatever it changed
  //   function  give the pass its own variable scope, named for {code function=...}
  //
  // A function pass has to run inside a real PHP function to get that scope, so it goes
  // through padStrFun() in lib/execute.php; every other pass goes straight to
  // start/pad/pad.php. Either way the pass's output is returned to the include.

  $padStrBox = padTagParm ( 'sandbox'  );
  $padStrRes = padTagParm ( 'reset'    );
  $padStrCln = padTagParm ( 'clean'    );
  $padStrFun = padTagParm ( 'function' );

  if ( $padStrFun )
    return padStrFun ( $padStrCod, $padStrBox, $padStrRes, $padStrCln, $padStrFun, $padStrBld );
  else
    return include PAD . 'start/pad/pad.php';

?>