<?php

  // The single form of {widget:notifications/}: the rows as a list - the title, the text when it says
  // more than the title, the time, the mark-read link - in a section a screen reader hears as
  // Notifications; "No new notifications." when there are none. Its style is
  // www/assets/channels/notifications.css, written once per page, the colours
  // --pad-notifications-* custom properties with light-dark() defaults. The html of a
  // notification is what its template rendered, the values in it escaped there.

  function padNotifyList ( $rows ) {

    $style = padNotifyListStyle ();

    if ( ! $rows )
      return $style . '<p class="pad-notifications pad-notifications-none">No new notifications.</p>';

    $items = '';

    foreach ( $rows as $row ) {

      $text   = trim ( $row ['text'] ) !== '' and trim ( $row ['text'] ) !== $row ['title'];
      $items .= '<li><span class="pn-title">' . padWidgetAttr ( $row ['title'] ) . '</span>'
              . '<time datetime="' . padWidgetAttr ( $row ['stamp'] ) . '">' . padWidgetAttr ( $row ['created'] ) . '</time>'
              . ( $text ? '<span class="pn-text">' . $row ['html'] . '</span>' : '' )
              . ( $row ['read'] ? '' : '<a class="pn-read" href="' . padWidgetAttr ( $row ['link'] ) . '">Mark read'
                                      . '<span class="pad-notifications-sr"> - ' . padWidgetAttr ( $row ['title'] ) . '</span></a>' )
              . '</li>';

    }

    return $style . '<section class="pad-notifications" aria-label="Notifications"><ul>' . $items . '</ul></section>';

  }

  function padNotifyWhen ( $time, $format ) {

    return ( new DateTimeImmutable ( "@$time" ) )->setTimezone ( padDateZone () )->format ( $format );

  }

  function padNotifyListStyle () {

    $rules = padWidgetFile ( 'channels/notifications.css' );

    return padWidgetStyle ( 'notifications', [ 'line'  => [ '#e3e5e8', '#3a3f46' ],
                                               'text'  => [ '#30343a', '#d5d9de' ],
                                               'muted' => [ '#6b7178', '#9aa1a9' ],
                                               'link'  => [ '#1d5fd1', '#7aa2f7' ] ], $rules );

  }

?>
