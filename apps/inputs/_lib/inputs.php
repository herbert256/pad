<?php

  // The rich form fields of the engine, as the engine reads them - the files of pad/inputs/types/
  // (padInputTypes) - each with what its own file says of it: the comment at its top, its line
  // the words after the dash up to the first colon or full stop.

  function inputsCatalog () {

    static $inputs = NULL;

    if ( $inputs !== NULL )
      return $inputs;

    $inputs = [];

    foreach ( padInputTypes () as $kind ) {

      $comment = [];

      foreach ( file ( PAD . "inputs/types/$kind.php", FILE_IGNORE_NEW_LINES ) as $line ) {
        if ( preg_match ( '/^\s*\/\/\s?(.*)$/', $line, $match ) ) {
          if ( trim ( $match [1] ) === '' )
            break;
          $comment [] = trim ( $match [1] );
        } elseif ( $comment )
          break;
      }

      $text = ucfirst ( preg_replace ( '/^' . preg_quote ( $kind, '/' ) . '\s+-\s+/', '', implode ( ' ', $comment ) ) );
      $line = rtrim ( preg_split ( '/[:.](\s|$)/', $text, 2 ) [0], ',;- ' ) . '.';

      $inputs [$kind] = [ 'kind' => $kind, 'line' => $line, 'text' => $text ];

    }

    return $inputs;

  }

  // The template a kind is tried with here: _inputs/<kind>.pad, a field or two of the kind with
  // the options that show it best, else the tag alone with a label.

  function inputsTemplate ( $kind ) {

    $file = APP . "_inputs/$kind.pad";

    return is_file ( $file ) ? trim ( file_get_contents ( $file ) ) : "{" . $kind . " '$kind', label='" . ucfirst ( $kind ) . "'/}";

  }

  // What the page's PHP received from a post of the form - the fields as $_POST holds them,
  // the engine's own left out, a long value (a signature) cut short, a file as its name, type
  // and size - for the table under the form.

  function inputsReceived () {

    $rows = [];

    foreach ( $_POST as $name => $value ) {
      if ( in_array ( $name, [ 'padForm', 'padFormAt', 'padCsrfToken' ], TRUE ) )
        continue;
      $text = is_array ( $value ) ? json_encode ( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : (string) $value;
      if ( strlen ( $text ) > 72 )
        $text = substr ( $text, 0, 60 ) . ' ... (' . strlen ( $text ) . ' characters)';
      $rows [] = [ 'name' => $name, 'value' => $text ];
    }

    foreach ( $_FILES as $name => $file )
      $rows [] = [ 'name' => $name, 'value' => is_array ( $file ['name'] ?? NULL )
                     ? implode ( ', ', array_filter ( $file ['name'] ) )
                     : ( ( $file ['error'] ?? UPLOAD_ERR_NO_FILE ) === UPLOAD_ERR_NO_FILE ? '(no file)'
                         : ( $file ['name'] ?? '' ) . ' - ' . ( $file ['type'] ?? '' ) . ', ' . padUploadSize ( (int) ( $file ['size'] ?? 0 ) ) ) ];

    return $rows;

  }

?>
