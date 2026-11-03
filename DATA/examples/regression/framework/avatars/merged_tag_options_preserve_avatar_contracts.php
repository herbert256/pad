<?php

$html = padUnprotect ( padUnescape ( padCode ( "{avatar:initials 'Ada Lovelace'/}{avatar:initials 'Ada', shape='square', size=32, label='Initials label'/}{avatar:identicon 'ada', title='Original title', label='Alias label'/}{avatar:identicon 'ada', label='Alias only'/}{avatar:rings 'secret-id', size=72, label='New rings', palette=[ '#123456', '#fedcba' ]/}" ) ) );
$answer = substr_count ( $html, '<svg ' ) === 5
  && str_contains ( $html, 'width="48" height="48"' )
  && str_contains ( $html, 'width="32" height="32"' )
  && str_contains ( $html, 'rx="18"' )
  && str_contains ( $html, 'aria-label="Initials label"' )
  && str_contains ( $html, 'aria-label="Original title"' )
  && ! str_contains ( $html, 'Alias label' )
  && str_contains ( $html, 'aria-label="Alias only"' )
  && str_contains ( $html, 'aria-label="New rings"' )
  && str_contains ( $html, 'width="72" height="72"' )
  && str_contains ( $html, '#123456' ) && str_contains ( $html, '#fedcba' )
  && ! str_contains ( $html, 'secret-id' )
  ? 'tag defaults, shape, title alias and palette preserved' : 'FAIL';

?>
