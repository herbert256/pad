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

  if ( str_contains ( $padParm, '://' ) or str_starts_with ( $padParm, '\\\\' ) )
    return FALSE;

  return padDirContained ( dirname ( $padParm ) ) and file_exists ( $padParm );

?>
