<?php

  // A list from the query string - type[]=x - is no type: the title made of it ended the
  // page on "Array to string conversion".

  if ( ! isset ( $type ) or ! is_string ( $type ) ) $type = 'Tags';
  if ( ! isset ( $xref ) ) $xref = 'tag';
  if ( ! isset ( $item ) ) $item = 'pad';

  // A reference that is not there - or a name that is no reference - is a page not found:
  // the listing of a missing directory ended on a PHP error and a 500.

  if ( ! referenceName ( $xref ) or ! referenceName ( $item ) or ! is_dir ( DATA . "reference/$xref/$item" ) )
    return padRefuse ( 404, 'There is no such reference' );

  $hits = [];

  foreach ( padFiles ( DATA . "reference/$xref/$item" ) as $file ) 
    $hits [$file] ['item'] = str_replace ( '.txt', '', $file );

  $xref = "$xref/$item";

  if ( count ($hits) == 1 )
    padRedirect ( 'pages',
                  [ 'type' => $type,
                    'xref' => $xref,
                    'item' => $hits [array_key_first($hits)] ['item'] ] );

  $title .= " - $type - $item";

?>
