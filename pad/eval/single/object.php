<?php

  // object: - reads the PHP variable called $name, whatever the page's .php file left in scope,
  // and flattens it to an array, so an object or resource can be walked like ordinary PAD data.
  //
  // Through $GLOBALS, because that is where a page's variables are: this file is included from
  // inside padEvalType(), so the variable variable it used to write reached that function's own
  // locals instead - object:myself handed back the evaluator's piped value, and no name a page
  // could define resolved at all.

  // An application's variable, never the engine's: object:padSqlPassword handed the
  // database password to whatever template asked.

  if ( padEngineName ( $name ) ) {
    padError ( "object: reads application variables - '$name' belongs to the engine" );
    return '';
  }

  return padToArray ( $GLOBALS [$name] ?? '' );

?>
