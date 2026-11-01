<?php

  // The rows of a Markdown collection - front matter as it is, title and date, a slug and the
  // body as HTML - written as the three feeds: the slug behind the feed's link= is an entry's
  // link and id, the newest date the feed's updated, an entry without a title or a date has
  // what each format needs - Atom an updated, RSS a title, JSON Feed an id - and everything is
  // escaped as its format has it. Atom has the minimum RFC 4287 asks: the feed's id, title,
  // updated and an author, each entry's id, title and updated.

  $feedPosts   = padCollection ( 'posts' );
  $feedOptions = [ 'title' => 'Posts & notes', 'link' => 'https://example.com/blog/', 'self' => 'https://example.com/blog/feed' ];

  $feedRss  = str_replace ( "\n", '', padFormatWrite ( 'rss',      $feedPosts, $feedOptions ) );
  $feedAtom = str_replace ( "\n", '', padFormatWrite ( 'atom',     $feedPosts, $feedOptions ) );
  $feedJson = str_replace ( "\n", '', padFormatWrite ( 'jsonfeed', $feedPosts, $feedOptions ) );

?>
