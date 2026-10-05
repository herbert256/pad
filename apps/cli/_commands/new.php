<?php

  // pad new <app>: a new application - apps/<app>/ with a first page pair and a README, and
  // the entry point www/<app>/index.php that serves it.
  //
  // pad new <app>/<dir>/<page>: a new page pair in an application that exists - the
  // application is the shortest part of the name that has an entry point in www/, so a
  // nested one like regression/pages works too. Nothing that exists is overwritten.

  $newName = trim ( $argv [2] ?? '', '/' );
  $home    = cliHome ();

  if ( ! cliName ( $newName ) )
    return cliFail ( "pad new <app> or pad new <app>/<page> - letters, digits, _ and -, parts joined with /" );

  $newParts = explode ( '/', $newName );
  $newApp   = '';

  for ( $i = 1; $i <= count ( $newParts ); $i++ ) {
    $newTry = implode ( '/', array_slice ( $newParts, 0, $i ) );
    if ( file_exists ( "$home/www/$newTry/index.php" ) and is_dir ( "$home/apps/$newTry" ) ) {
      $newApp = $newTry;
      break;
    }
  }

  $newMade = [];

  if ( $newApp === '' ) {

    if ( count ( $newParts ) > 1 and ! is_dir ( "$home/apps/{$newParts[0]}" ) )
      return cliFail ( "there is no application '{$newParts[0]}' to put a page in - pad new {$newParts[0]} first" );

    if ( file_exists ( "$home/apps/$newName" ) or file_exists ( "$home/www/$newName" ) )
      return cliFail ( "apps/$newName or www/$newName exists already" );

    $newTitle = ucfirst ( basename ( $newName ) );

    $newFiles = [
      "apps/$newName/index.php" => "<?php\n\n  // The data of the home page: what this file sets, index.pad shows.\n\n  \$hello = 'Hello from $newTitle!';\n\n?>",
      "apps/$newName/index.pad" => "<h1>{\$hello}</h1>\n",
      "apps/$newName/README.md" => "# $newTitle\n\n## Introduction\n\nA PAD application made by `pad new $newName`. Each page is a pair: `<page>.php` sets the\ndata, `<page>.pad` renders it. Add one with `pad new $newName/<page>`.\n",
      "www/$newName/index.php"  => "<?php\n\n  include __DIR__ . '/" . str_repeat ( '../', count ( $newParts ) ) . "pad.php';\n\n?>\n"
    ];

  } else {

    $newPage = substr ( $newName, strlen ( $newApp ) + 1 );

    if ( $newPage === '' or $newPage === FALSE )
      return cliFail ( "the application $newApp exists already - pad new $newApp/<page> adds a page" );

    foreach ( [ 'php', 'pad', 'html' ] as $newExt )
      if ( file_exists ( "$home/apps/$newApp/$newPage.$newExt" ) )
        return cliFail ( "apps/$newApp/$newPage.$newExt exists already" );

    $newTitle = ucfirst ( basename ( $newPage ) );

    $newFiles = [
      "apps/$newApp/$newPage.php" => "<?php\n\n  // The data of ?$newPage - what this file sets, " . basename ( $newPage ) . ".pad shows.\n\n  \$title = '$newTitle';\n\n?>",
      "apps/$newApp/$newPage.pad" => "<h1>{\$title}</h1>\n"
    ];

  }

  foreach ( $newFiles as $newFile => $newText ) {

    if ( ! is_dir ( dirname ( "$home/$newFile" ) ) )
      mkdir ( dirname ( "$home/$newFile" ), 0755, TRUE );

    file_put_contents ( "$home/$newFile", $newText );

    cliOut ( "made $newFile" );

  }

  if ( $newApp === '' )
    cliOut ( "\nOpen it with pad serve, at http://127.0.0.1:8000/$newName/ - or pad render $newName" );
  else
    cliOut ( "\nOpen it at ?$newPage - or pad render $newApp $newPage" );

  return 0;

?>
