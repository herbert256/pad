<?php

  // The addresses of Twitch - a channel, a video with the moment of its ?t=, a collection, a
  // clip on twitch.tv or clips.twitch.tv, and the player's own - are read as what they name.
  // A page of Twitch's own that stands where a channel would (directory, videos ...), a host
  // that only looks like Twitch's and a player address without anything to play are none of
  // its addresses.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( function ( $url ) {
    $found = padEmbedFind ( $url );
    return $found ? "{$found [0]}:{$found [1] ['what']}:{$found [1] ['id']}" . ( $found [1] ['start'] ? "@{$found [1] ['start']}" : '' ) : 'none';
  }, [
    'https://www.twitch.tv/TwitchGaming',
    'https://m.twitch.tv/twitchgaming/',
    'https://www.twitch.tv/videos/2290520135?t=1h2m3s',
    'https://www.twitch.tv/collections/abcDeF1ghIJ2kL',
    'https://www.twitch.tv/twitchgaming/clip/AwkwardHelplessSalamanderSwiftRage-a1B2c3D4',
    'https://clips.twitch.tv/AwkwardHelplessSalamanderSwiftRage',
    'https://clips.twitch.tv/embed?clip=AwkwardHelplessSalamanderSwiftRage&parent=example.com',
    'https://player.twitch.tv/?channel=twitchgaming&parent=example.com',
    'https://player.twitch.tv/?video=v2290520135&time=90s',
    'https://www.twitch.tv/directory',
    'https://www.twitch.tv/twitchgaming/videos',
    'https://twitch.tv.example.org/twitchgaming',
    'https://player.twitch.tv/?parent=example.com',
  ] ) );

?>
