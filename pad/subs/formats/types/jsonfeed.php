<?php

  // jsonfeed - JSON Feed 1.1 (jsonfeed.org), written: the feed's title=, link=, self=,
  // description=, author= and an item per row - its id, url, title, content_html or
  // content_text, summary and date_published (title, link, date, summary, body and id of a
  // row, or the front matter of a Markdown collection: subs/formats/_lib/feeds.php). Read
  // only as the JSON it is, by json.

  return [
    'name'      => 'JSON Feed',
    'mime'      => 'application/feed+json',
    'extension' => [ 'json' ],
    'shape'     => 'rows',
    'lib'       => 'feeds',
    'write'     => fn ( $data, $options ) => padJsonFeedWrite ( padFormatRows ( $data ), $options )
  ];

?>
