<?php

  // The addresses of TikTok - a video or a photo post of an account, the embed player's own
  // address and the older embed form - are read as the number of the post, and the account
  // where the address names it. A short vm.tiktok.com link, an account's page and a host
  // that only looks like TikTok's are none of its addresses: the first has to be followed to
  // find the post.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  $found = implode ( ' ', array_map ( function ( $url ) {
    $found = padEmbedFind ( $url );
    return $found ? "{$found [0]}:{$found [1] ['id']}" . ( $found [1] ['user'] !== '' ? ":@{$found [1] ['user']}" : '' ) : 'none';
  }, [
    'https://www.tiktok.com/@scout2015/video/6718335390845095173',
    'https://www.tiktok.com/@scout2015/photo/7246754453284138286?lang=en',
    'https://www.tiktok.com/player/v1/6718335390845095173?autoplay=1',
    'https://www.tiktok.com/embed/v2/6718335390845095173',
    'https://m.tiktok.com/@scout2015/video/6718335390845095173/',
    'https://vm.tiktok.com/ZMhvqjqjq/',
    'https://www.tiktok.com/@scout2015',
    'https://www.tiktok.com.example.org/@scout2015/video/6718335390845095173',
  ] ) );

?>
