<?php

$html = padUnprotect ( padUnescape ( padCode ( "{image:figure 'assets/image-focus.png', width=16, alt='Red and blue', caption='<script>caption</script>'/}" ) ) );
$answer = ! padTagBuiltIn ( 'img' ) && ! padTagLib ( 'img' )
  && padKinds ( 'images' ) === [ 'contactsheet', 'country', 'emoji', 'figure', 'picture', 'socialcard', 'sprite', 'thumbnail', 'watermark' ]
  && str_contains ( $html, '<figcaption>&lt;script&gt;caption&lt;/script&gt;</figcaption>' )
  ? 'nine image kinds with safe captions and no old tag' : 'FAIL';

?>
