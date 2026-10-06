<?php

  // Makes a file, a directory, a page pair, or the files of one of PAD's kinds - a tag, a
  // pipe function, a snippet, a guard, a test ... - from the starters in _templates/. A kind
  // goes where PAD looks for it: _tags/, _functions/ and the like below the directory asked
  // for. Nothing that exists is overwritten: when one of the files is there already,
  // none is made.

  [ $app, $root, $dir ] = [ editApp ( editArg ( $body, 'app' ) ), editArg ( $body, 'root', 'app' ), trim ( editArg ( $body, 'dir' ), '/' ) ];

  $name = trim ( editArg ( $body, 'name' ), '/' );
  $kind = editArg ( $body, 'kind', 'file' );
  $base = editPath ( $app, $root, $dir );

  if ( ! is_dir ( $base ) )
    editFail ( 'the directory is not there' );

  $at   = fn ( $rel ) => $dir === '' ? $rel : "$dir/$rel";
  $word = '/^[A-Za-z_][A-Za-z0-9_-]*$/';
  $path = '/^[A-Za-z0-9_][A-Za-z0-9_.-]*(\/[A-Za-z0-9_][A-Za-z0-9_.-]*)*$/';

  $title = ucfirst ( str_replace ( [ '_', '-' ], ' ', basename ( $name ) ) );
  $files = [];
  $open  = NULL;

  $need = function ( $pattern, $what ) use ( $name ) {
    if ( ! preg_match ( $pattern, $name ) )
      editFail ( "'" . padMakeSafe ( $name, 60 ) . "' is no $what" );
  };

  switch ( $kind ) {

    case 'dir':
      foreach ( explode ( '/', $name ) as $part )
        editName ( $part );
      $target = editPath ( $app, $root, $at ( $name ) );
      editCreate ( $target, '', TRUE );
      return [ 'made' => [ $at ( $name ) ], 'open' => NULL ];

    case 'file':
      foreach ( explode ( '/', $name ) as $part )
        editName ( $part );
      $files [ $at ( $name ) ] = editStarter ( $name );
      break;

    case 'page':
      $need ( '/^[A-Za-z0-9_-]+(\/[A-Za-z0-9_-]+)*$/', 'page name - letters, digits, _ and -' );
      if ( $root != 'app' )
        editFail ( 'pages live in the application, not in www/' );
      $files [ $at ( "$name.php" ) ] = 'page.php';
      $files [ $at ( "$name.pad" ) ] = 'page.pad';
      $open = $at ( "$name.pad" );
      break;

    case 'tag':       $need ( $word, 'tag name' );      $files [ $at ( "_tags/$name.php" ) ]      = 'tag.php';       break;
    case 'component': $need ( $word, 'tag name' );      $files [ $at ( "_tags/$name.pad" ) ]      = 'component.pad'; break;
    case 'function':  $need ( $word, 'function name' ); $files [ $at ( "_functions/$name.php" ) ] = 'function.php';  break;
    case 'include':   $need ( $word, 'snippet name' );  $files [ $at ( "_include/$name.pad" ) ]   = 'include.pad';   break;
    case 'callback':  $need ( $word, 'callback name' ); $files [ $at ( "_callbacks/$name.php" ) ] = 'callback.php';  break;
    case 'option':    $need ( $word, 'option name' );   $files [ $at ( "_options/$name.php" ) ]   = 'option.php';    break;
    case 'data':      $need ( $word, 'data name' );     $files [ $at ( "_data/$name.json" ) ]     = 'data.json';     break;
    case 'query':     $need ( $word, 'query name' );    $files [ $at ( "_data/$name.sql" ) ]      = 'query.sql';     break;
    case 'mail':      $need ( $word, 'template name' ); $files [ $at ( "_mail/$name.pad" ) ]      = 'mail.pad';      break;
    case 'lang':      $need ( '/^[a-z]{2,3}([_-][A-Za-z]{2,4})?$/', 'locale like en or nl' );
                      $files [ $at ( "_lang/$name.json" ) ] = 'lang.json';
                      break;
    case 'content':   $need ( '/^[A-Za-z0-9_-]+\/[A-Za-z0-9_-]+$/', 'collection/name' );
                      $files [ $at ( "_content/$name.md" ) ] = 'content.md';
                      break;
    case 'event':     if ( ! in_array ( $name, [ 'error', 'sql', 'curl', 'output' ], TRUE ) )
                        editFail ( 'an event hook is error, sql, curl or output' );
                      $files [ $at ( "_events/$name.php" ) ] = "event-$name.php";
                      break;
    case 'guard':     $files [ $at ( '_guard.php' ) ]        = 'guard.php';   break;
    case 'config':    $files [ $at ( '_config/config.php' ) ] = 'config.php'; break;
    case 'wrapper':   $files [ $at ( '_inits.pad' ) ]        = 'inits.pad';
                      $files [ $at ( '_exits.pad' ) ]        = 'exits.pad';
                      $open = $at ( '_inits.pad' );
                      break;
    case 'test':      $need ( $word, 'test name' );
                      $files [ $at ( "_tests/$name.pad" ) ] = 'test.pad';
                      $files [ $at ( "_tests/$name.txt" ) ] = 'test.txt';
                      $open = $at ( "_tests/$name.pad" );
                      break;

    default:
      editFail ( "there is no kind of file named '" . padMakeSafe ( $kind, 30 ) . "'" );

  }

  if ( $kind != 'file' and $root != 'app' )
    editFail ( 'PAD files live in the application, not in www/' );

  // Every place first, then the writing: all or nothing.

  $targets = [];

  foreach ( $files as $rel => $template ) {
    $target = editPath ( $app, $root, $rel );
    if ( file_exists ( $target ) )
      editFail ( "$rel exists already" );
    $targets [$rel] = $target;
  }

  foreach ( $files as $rel => $template ) {
    $text = ( $kind == 'file' ) ? $template : editTemplate ( $template, $name, $title, $app );
    editCreate ( $targets [$rel], $text );
  }

  return [ 'made' => array_keys ( $files ), 'open' => $open ?? array_key_last ( $files ) ];

?>
