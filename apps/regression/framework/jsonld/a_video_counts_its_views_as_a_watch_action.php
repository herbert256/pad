<?php

  // A video as a database answers it: its thumbnails a list, its length in words, its views a
  // number - and its owner's e-mail address, which never reaches the page.

  $clip = [ 'name' => 'Making pea soup', 'description' => 'From dry peas to a thick soup.',
            'thumbnail' => [ 'soup-1x1.jpg', 'https://cdn.example/soup-16x9.jpg' ], 'uploadDate' => '2026-11-01T10:00:00+01:00',
            'duration' => '4 min 12 s', 'file' => '/media/soup.mp4', 'author' => 'Ann Smith', 'views' => 2347,
            'owner' => 'ann@example.org' ];

?>
