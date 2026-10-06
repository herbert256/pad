<?php

  // The starter text of new files. A kind's starters are the .txt files of _templates/ -
  // text only: no PAD runs over them, and no tool takes them for templates of this
  // application. __NAME__, __TITLE__ and __APP__ in them are the name given, that name made
  // readable, and the application.

  function editTemplate ( $template, $name, $title, $app ) {

    $file = APP . "_templates/$template.txt";

    if ( ! preg_match ( '/^[a-z-]+\.[a-z]+$/', $template ) or ! is_file ( $file ) )
      editFail ( "there is no starter named '$template'" );

    return strtr ( (string) file_get_contents ( $file ), [ '__NAME__' => $name, '__TITLE__' => $title, '__APP__' => $app ] );

  }

  // A plain new file: empty, except that a PHP file gets its tags.

  function editStarter ( $name ) {

    return match ( editExt ( $name ) ) {
      'php'   => "<?php\n\n  \n\n?>\n",
      'json'  => "{\n}\n",
      default => ''
    };

  }

?>
