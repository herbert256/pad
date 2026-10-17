<?php

  // A link holding a picture the document cannot draw - a PNG, as most linked logos are, an
  // SVG, a remote picture - keeps the alt text in its line, as the link it was, where it
  // became a grey [alt] of its own: no link annotation, and the sentence round it broken in
  // three lines.

  $pdf = padPdf ( '<p>Write to <a href="mailto:info@example.com"><img src="mail.png" alt="info"></a> or call us.</p>',
                  [ 'compress' => FALSE ] );

  preg_match_all ( '#/URI \(([^)]*)\)#', $pdf, $links );
  preg_match_all ( '#\(([^()]*)\) Tj#', $pdf, $texts );

  $kept = 'links: ' . implode ( ' ', $links [1] ) . ', text: ' . implode ( '|', $texts [1] );

?>
