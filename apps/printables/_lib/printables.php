<?php

  // The printables of the engine, as the engine reads them - the files of
  // pad/subs/printables/types/ (padPrintableKinds) - each with what its own file says of it:
  // the comment at its top, its syntax first, then a dash and what it is; its name the plural
  // its plan gives itself, [ 'index card', 'index cards' ].

  function printablesCatalog () {

    $printables = [];

    foreach ( padPrintableKinds () as $kind ) {

      $file    = (string) padFileGet ( PAD . "subs/printables/types/$kind.php" );
      $comment = [];

      foreach ( explode ( "\n", $file ) as $line ) {
        if ( preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
          if ( trim ( $match [1] ) === '' )
            break;
          $comment [] = trim ( $match [1] );
        } elseif ( $comment )
          break;
      }

      // What it is: the comment after its syntax and the dash, its first letter a capital;
      // the line of its card the first sentence of that, up to its colon.

      $flat = implode ( ' ', $comment );
      $text = ( $at = strpos ( $flat, '} - ' ) ) !== FALSE ? substr ( $flat, $at + 4 ) : $flat;
      $text = ucfirst ( $text );
      $line = rtrim ( preg_split ( '/:\s|\.\s/', $text, 2 ) [0], '.' ) . '.';

      $name = preg_match ( "/\\[ '([a-z ]+)', '([a-z ]+)' \\]/", $file, $match ) ? ucfirst ( $match [2] ) : ucfirst ( $kind );

      $printables [$kind] = [ 'kind' => $kind, 'name' => $name, 'line' => $line, 'text' => $text ];

    }

    return $printables;

  }

  // The template a kind is shown with here: kinds/<kind>.pad - a page of its own too - else the
  // tag around a line of text.

  function printablesTemplate ( $kind ) {

    $file = APP . "kinds/$kind.pad";

    return is_file ( $file ) ? trim ( file_get_contents ( $file ) ) : "{printable:$kind scale=0.45, pdf}\n  A $kind\n{/printable:$kind}";

  }

?>
