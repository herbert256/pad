<?php

  // Every address goes to the provider whose file of embeds/types/ knows it - the ones that
  // know their host first, the generic ones (a PeerTube instance, a file by its extension)
  // after them - and an address none knows to none; a bare id is no address unless the
  // provider is named.

  include_once PAD . 'embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( fn ( $url ) => padEmbedFind ( $url ) [0] ?? 'none', [
    'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
    'https://youtu.be/aqz-KE-bpKQ?t=90',
    'https://vimeo.com/channels/staffpicks/76979871',
    'https://framatube.org/w/kkGMgK9ZtnKfYAgnEtQxbv',
    'https://dai.ly/x8j4k2c',
    'https://archive.org/details/night_of_the_living_dead',
    'https://soundcloud.com/forss/flickermood',
    'https://open.spotify.com/intl-nl/track/4uLU6hMCjMI75M1A2tKUQC',
    'spotify:album:4uLU6hMCjMI75M1A2tKUQC',
    'https://codepen.io/team/codepen/pen/PNaGbb',
    'https://www.openstreetmap.org/#map=15/52.3731/4.8922',
    'clips/sunrise.webm',
    'https://example.org/sounds/bell.mp3',
    'docs/manual.pdf',
    'aqz-KE-bpKQ',
    'https://example.com/movie',
    'javascript:alert(1).mp4',
  ] ) );

?>
