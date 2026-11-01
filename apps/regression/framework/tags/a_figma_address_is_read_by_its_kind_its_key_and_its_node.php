<?php

  // The addresses of Figma - a design, the old /file/ form of one, a prototype, a FigJam
  // board, a slide deck, on www.figma.com or embed.figma.com, the file's name after the key
  // or not - are read as their kind, their key and the node-id they open on (0:1 as 0-1). A
  // branch of a file, a page of the community, a key too short to be one and a host that
  // only looks like Figma's are none of its addresses.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( function ( $url ) {
    $found = padEmbedFind ( $url );
    return $found ? "{$found [0]}:{$found [1] ['what']}:{$found [1] ['key']}" . ( $found [1] ['node'] !== '' ? ":{$found [1] ['node']}" : '' ) : 'none';
  }, [
    'https://www.figma.com/design/Kf3TqZ8yWb1nLp0sR4vXcA/Shop-Checkout?node-id=12-34&t=AbC',
    'https://figma.com/file/Kf3TqZ8yWb1nLp0sR4vXcA/Shop-Checkout?node-id=0%3A1',
    'https://www.figma.com/proto/Kf3TqZ8yWb1nLp0sR4vXcA/Shop?node-id=5-6&starting-point-node-id=5%3A6',
    'https://www.figma.com/board/Kf3TqZ8yWb1nLp0sR4vXcA',
    'https://www.figma.com/slides/Kf3TqZ8yWb1nLp0sR4vXcA/Pitch/',
    'https://www.figma.com/deck/Kf3TqZ8yWb1nLp0sR4vXcA/Pitch',
    'https://embed.figma.com/design/Kf3TqZ8yWb1nLp0sR4vXcA?embed-host=share',
    'https://www.figma.com/design/Kf3TqZ8yWb1nLp0sR4vXcA/branch/Pq2RsT3uVw4XyZ5aBc6DeF/Shop',
    'https://www.figma.com/community/file/1035203688168086460',
    'https://www.figma.com/design/short/Shop',
    'https://www.figma.com.example.org/design/Kf3TqZ8yWb1nLp0sR4vXcA/Shop',
  ] ) );

?>
