<?php

  // rss - RSS 2.0, written: a channel of the feed's title=, link= and description=, an item
  // per row - its title, link, date, summary and id (title, link, date, summary, body and
  // id of a row, or the front matter of a Markdown collection: subs/formats/_lib/feeds.php).
  // Read only as the XML it is - an RSS document is read by xml, its items its rows.

  return [
    'name'      => 'RSS',
    'mime'      => 'application/rss+xml',
    'extension' => [ 'rss' ],
    'shape'     => 'rows',
    'lib'       => 'feeds',
    'write'     => fn ( $data, $options ) => padRssWrite ( padFormatRows ( $data ), $options )
  ];

?>
