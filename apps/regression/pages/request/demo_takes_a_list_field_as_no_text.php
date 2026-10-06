<?php

  // The demo's forms take a field sent as a list - name[]=x, which anyone can post - as no
  // text at all: the guestbook asks for both fields again, the todo list adds no task, the
  // contact form stores nothing, and ?todo&message[]=x shows the list, where each answered
  // a 500 from htmlspecialchars() or the template. Nothing is written, so the request/todo
  // and request/guestbook round-trips beside this one are left alone. The demo checks CSRF
  // tokens, so the posts carry the token of a form with the session cookie it belongs to,
  // and they go urlencoded: an array handed to curl as it is arrives as plain fields.

  $listForm  = padCurl ( $padHost . 'demo/?guestbook&padInclude' );
  $listToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $listForm ['data'], $listMatch ) ? $listMatch [1] : '';
  $listJar   = [ 'PHPSESSID' => $listForm ['cookies'] ['PHPSESSID'] ?? '' ];

  $listBook = padCurl ( [ 'url' => $padHost . 'demo/?guestbook&padInclude', 'cookies' => $listJar,
                          'post' => http_build_query ( [ 'action' => 'add', 'name' => [ 'x' ], 'comment' => 'list marker', 'padCsrfToken' => $listToken ] ) ] );

  $listTodo = padCurl ( [ 'url' => $padHost . 'demo/?todoPost', 'cookies' => $listJar,
                          'post' => http_build_query ( [ 'go' => 'add', 'task' => [ 'list marker' ], 'padCsrfToken' => $listToken ] ) ] );

  $listMail = padCurl ( [ 'url' => $padHost . 'demo/?contact&padInclude', 'cookies' => $listJar,
                          'post' => http_build_query ( [ 'padForm' => 'contact', 'name' => [ 'x' ], 'email' => 'list@example.com',
                                      'subject' => 'list marker', 'message' => 'list marker', 'padCsrfToken' => $listToken ] ) ] );

  $listShow = padCurl ( $padHost . 'demo/?todo&message[]=x&padInclude' );

  $listStored = FALSE;

  foreach ( [ 'guestbook', 'todos', 'messages' ] as $listStore )
    if ( str_contains ( padFileGet ( "demo/$listStore.json" ), 'list marker' ) )
      $listStored = TRUE;

  $listResult = 'guestbook ' . $listBook ['result']
              . ( str_contains ( $listBook ['data'], 'Please fill in both' ) ? ' asked again' : '' )
              . ', todo ' . $listTodo ['result'] . ', contact ' . $listMail ['result']
              . ', message ' . $listShow ['result'] . ', stored: ' . ( $listStored ? 'yes' : 'no' );

?>
