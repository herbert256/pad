<?php

  // atom - Atom (RFC 4287), written: a feed with its id, title, updated and author, an entry
  // per row - its id, title, updated, link, summary and content (title, link, date, summary,
  // body and id of a row, or the front matter of a Markdown collection:
  // subs/formats/_lib/feeds.php). The feed's options: title=, link=, self=, description=,
  // author=, id=, updated=. Read only as the XML it is, by xml.

  return [
    'name'      => 'Atom',
    'mime'      => 'application/atom+xml',
    'extension' => [ 'atom' ],
    'shape'     => 'rows',
    'lib'       => 'feeds',
    'write'     => fn ( $data, $options ) => padAtomWrite ( padFormatRows ( $data ), $options )
  ];

?>
