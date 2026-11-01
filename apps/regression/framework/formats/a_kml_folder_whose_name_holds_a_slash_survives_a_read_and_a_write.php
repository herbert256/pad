<?php

  // A folder named Rotterdam / Den Haag - or AC/DC - is one folder: the reader writes a / inside
  // a name as ∕ (U+2215), which the / between nested folders is not, and the writer writes it
  // as / again, so a document read and written keeps its folders - where it came back as a
  // folder Rotterdam holding a folder Den Haag.

  $kmlText = '<kml xmlns="http://www.opengis.net/kml/2.2"><Document>'
           . '<Folder><name>Rotterdam / Den Haag</name><Placemark><name>Station</name></Placemark>'
           . '<Folder><name>AC/DC</name><Placemark><name>Stage</name></Placemark></Folder></Folder>'
           . '</Document></kml>';

  $kmlRows  = padFormatRead ( 'kml', $kmlText );
  $kmlRead  = implode ( ' | ', array_column ( $kmlRows, 'folder' ) );
  $kmlAgain = padFormatWrite ( 'kml', $kmlRows );

  preg_match_all ( '#<(/?)Folder>(?:\s*<name>([^<]*))?#', $kmlAgain, $kmlMatch, PREG_SET_ORDER );

  $kmlTree = implode ( ' ', array_map ( fn ( $m ) => $m [1] === '/' ? ']' : '[' . $m [2], $kmlMatch ) );
  $kmlSame = ( padFormatRead ( 'kml', $kmlAgain ) == $kmlRows ) ? 'the same rows' : 'other rows';

?>
