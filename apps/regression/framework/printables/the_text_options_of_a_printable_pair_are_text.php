<?php

  // A text option of a printable - sender=, title=, signed= - is the characters it holds in the
  // pair form as in the single tag: a value never becomes template code. The pair writes its
  // sheets on the end walk, into content the level walks again, and a field's {pad:echo 6*7/} in
  // sender= ran there - X42Y in the return address of the envelope and the letter, the
  // invoice's heading, the band of the badges, the certificate's title and its signature.

  $v = 'X{pad:echo 6*7/}Y';

  $answer = [];

  foreach ( [ 'envelope' => 'sender=$v', 'letter' => 'sender=$v', 'invoice' => 'title=$v', 'badges' => 'title=$v',
              'certificate' => 'title=$v, signed=$v' ] as $kind => $options ) {
    $out       = padUnprotect ( padUnescape ( padCode ( "{printable:$kind $options}Ann{/printable:$kind}{printable:$kind $options/}" ) ) );
    $answer [] = "$kind: " . substr_count ( $out, 'X{pad:echo 6*7/}Y' ) . ' as text, ' . substr_count ( $out, 'X42Y' ) . ' run';
  }

  $answer = implode ( ' // ', $answer );

?>
