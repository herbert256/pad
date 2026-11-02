<?php

  // {image:thumbnail 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'} - a picture
  // of www/<application>/ as a thumbnail of the size the page shows it at, made once with
  // GD and kept in www/<application>/_thumbs/ for the web server to send (subs/images/_lib/img.php).
  // width= and height= are the box; fit= cover (default: fill it, cut around the centre) or
  // contain (all of it inside); format= jpeg, png or webp, else the source's; quality= 1 to
  // 100 (80). The <img> carries its width and height, loading="lazy", decoding="async" and
  // a srcset with the picture at twice the size, as far as the source reaches.
  //
  // alt= is required: a picture without one is read out by its file name. alt='' says the
  // picture is decoration, and a screen reader passes it by.
  //
  // filter= is a comma list of the filters of subs/images/filters/types/, applied in the order
  // written, each reading its own options from the tag - colors=, levels=, cell=, radius=,
  // amount=, columns=, chars=, components=:
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='...'/}
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='grayscale, dither', alt='...'/}
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='halftone', cell=6, alt='...'/}
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='blurhash', alt='...'/}

  return padImg ( $padParm, padImageOptions () );

?>
