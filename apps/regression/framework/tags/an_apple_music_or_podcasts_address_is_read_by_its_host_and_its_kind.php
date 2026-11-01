<?php

  // The addresses of Apple Music and Apple Podcasts - and their old itunes.apple.com form -
  // go to the provider of their kind, as Apple's own embed code reads them: a country, the
  // kind, a name that may be left out and the id; a song or an episode with ?i=. A host that
  // only looks like Apple's - a longer name, a user name in front of another host - a kind
  // Apple has no player for and an id that is no number are none of theirs.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( fn ( $url ) => padEmbedFind ( $url ) [0] ?? 'none', [
    'https://music.apple.com/us/album/abbey-road-2019-mix/1474815798',
    'https://music.apple.com/nl/album/1474815798?i=1474815898',
    'https://music.apple.com/us/playlist/todays-hits/pl.f4d106fed2bd41149aaacabb233eb5eb',
    'https://embed.music.apple.com/gb/song/come-together/1474815898',
    'https://itunes.apple.com/us/album/abbey-road/id1474815798',
    'https://podcasts.apple.com/us/podcast/the-daily/id1200361736',
    'https://podcasts.apple.com/us/podcast/1200361736?i=1000600000000',
    'https://itunes.apple.com/us/podcast/the-daily/id1200361736',
    'https://music.apple.com.example.org/us/album/abbey-road/1474815798',
    'https://music.apple.com/us/app/abbey-road/1474815798',
    'https://podcasts.apple.com@example.org/us/podcast/the-daily/id1200361736',
    'https://music.apple.com/us/album/abbey-road/<b>',
  ] ) );

?>
