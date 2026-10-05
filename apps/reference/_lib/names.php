<?php

  // The reference a request names: $xref a directory under DATA/reference/, $item a name in
  // it - both straight from the query string, and the item names are whatever the harvest
  // met, quotes, parentheses and @ among them. What is held to is the tree: every part of
  // the name a real one, no '..' or '.' or empty part, so the path never climbs out of
  // DATA/reference/ - ?dir&xref=..&item=.. listed the repository's root, and ?pages read
  // any .txt file it named. A list from the query string ([] in the name) is no name.

  function referenceName ( $name ) {

    if ( ! is_string ( $name ) or $name === '' or preg_match ( '/[\x00-\x1f\\\\]/', $name ) )
      return FALSE;

    foreach ( explode ( '/', $name ) as $part )
      if ( $part === '' or $part === '.' or $part === '..' )
        return FALSE;

    return TRUE;

  }

?>
