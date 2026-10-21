<?php

  // {exists '/path/to/file'} is a plain file test: TRUE when the path is there, so the
  // content renders only then and the @else@ branch otherwise. The path is used exactly
  // as given - none of PAD's app, data or _include lookups apply.
  //
  // It answers for the tree {dir} and {files} keep to - the applications, the engine and
  // DATA - and for no other host: file_exists takes any stream wrapper and any path of the
  // machine, so {exists $v} told a visitor which files lay outside the tree - ?v=/etc/
  // master.passwd answered yes - and ?v=ftp://host:port/x had the server open an FTP session
  // to the host and port he named (a \\host\share path on Windows an SMB connection). The
  // directory is judged, so a file that is not there answers no as before.
  //
  // www/ counts as well: the applications' public files are served to every visitor anyway,
  // and a picture shown only when its file is there - www/<app>/images/logo.png - answered
  // missing.

  if ( str_contains ( $padParm, '://' ) or str_starts_with ( $padParm, '\\\\' ) )
    return FALSE;

  $padExistsDir = padGetPath ( dirname ( $padParm ) );
  $padExistsWww = padGetPath ( APPS . '../www' );

  $padExistsIn  = ( padDirContained ( dirname ( $padParm ) )
                    or ( $padExistsDir !== FALSE and $padExistsWww !== FALSE
                         and str_starts_with ( rtrim ( $padExistsDir, '/' ) . '/', rtrim ( $padExistsWww, '/' ) . '/' ) ) );

  return $padExistsIn and file_exists ( $padParm );

?>
