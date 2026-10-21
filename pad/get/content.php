<?php

  // Resolves the name in $padGetName to content and returns it: a stored {content} block
  // first, then an app _include/ snippet, then an app page; '' when nothing of that name
  // exists.
  //
  // Reached from types/content.php, the content= option and options/_go/reset.php (else=,
  // null=, notOk=, error=), which name their option in $padGetOption; the return value of
  // the include is the content.

  // A number is the name it writes - content=$n with an int from PHP names what a written
  // content=5 names - and anything else that is no text, the bare option {e else} among
  // them, names nothing.

  if ( is_int ( $padGetName ) or is_float ( $padGetName ) )
    $padGetName = (string) $padGetName;
  elseif ( ! is_string ( $padGetName ) )
    return '';

  if ( isset ( $padContentStore [$padGetName] ) )
    return $padContentStore [$padGetName];

  // The name is often a value - content=$name, else=$name - so it names content of this
  // application and nothing else: with a .. segment, a backslash, a NUL or a leading / it
  // walked out of the application, and the .php it reached - another application's page,
  // any file on the disk - was included and run in the request (padAppCheck refuses the
  // same names for every lookup).

  if ( str_contains ( $padGetName, "\0" ) or str_contains ( $padGetName, '\\' )
       or str_starts_with ( $padGetName, '/' ) or in_array ( '..', explode ( '/', $padGetName ), TRUE ) )
    return padError ( "the content name '" . padMakeSafe ( $padGetName, 60 ) . "' leaves the application - content=, else=, null= and notOk= name a content block, a snippet or a page" );

  if ( padAppIncludeCheck ( $padGetName ) ) return include PAD . 'get/include.php';

  // A page is a name a request could ask for: no segment starting with _ or ., which are
  // the application's own directories - _config/config, _jobs/x or _seeds/x ran as a page.

  if ( ! preg_match ( '#(^|/)[._]#', $padGetName ) and padAppPageCheck ( $padGetName ) )
    return include PAD . 'get/page.php';

  // Nothing else: a name that is no content once fell through to the tags and was rendered
  // as one over the content - a value getcwd, phpinfo or session_destroy called that PHP
  // function, an application tag ran on the author's text. A name written in the template
  // cannot be told apart from a value spliced into it, content={$v}, so no name does.
  //
  // Written in the template - $padGetOption names the option, padOptionLiteral says - a
  // name that is none of them is a miss the strict check reports, as {content:nosuch} is:
  // else='emoji' showed nothing in silence. A value naming nothing stays silent.

  if ( $padCheckSyntax and ( $padGetOption ?? '' ) !== '' and padOptionLiteral ( $padGetOption ) )
    padError ( "there is no content block, snippet or page named '" . padMakeSafe ( $padGetName, 60 ) . "' for $padGetOption=" );

  return '';

?>
