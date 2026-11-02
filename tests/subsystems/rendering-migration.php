<?php

  // Exercise the real parser and page lifecycle in a fresh process for each case.
  // These boundaries used to depend on the pad: namespace, rather than the renderer.
  $root = dirname ( __DIR__, 2 );
  $cases = [
    'late table of contents' => [
      "{widget:toc/}<h2>Later heading</h2>",
      [ '<nav', 'href="#later-heading"', 'id="later-heading"' ] ],
    'raw highlighted template' => [
      "{widget:highlight 'pad'}{pad:echo 'inert'/}{/widget:highlight}",
      [ 'pad-highlight', 'pad:echo', 'inert' ] ],
    'rendered markdown' => [
      "{widget:markdown}## {pad:echo 'Heading'/}\n\n**Strong**{/widget:markdown}",
      [ '<h2>Heading</h2>', '<strong>Strong</strong>' ] ],
    'nested widget items' => [
      "{widget:tabs}{pad:if 1}{widget:tab 'One'}First{/widget:tab}{/pad:if}{widget:tab 'Two'}Second{/widget:tab}{/widget:tabs}",
      [ 'First', 'Second', 'One', 'Two', 'pad-tabs' ] ],
    'native form rules before render' => [
      "{input:form 'contact'}{input:field 'email', type='email', rules='required|email', placeholder='you@example.org'/}{input:textarea 'message', rules='required', rows=3/}{/input:form}",
      [ 'name="padForm" value="contact"', 'type="email"', 'placeholder="you@example.org"', '<textarea', 'rows="3"' ] ],
    'rules in a highlight remain text' => [
      "{widget:highlight 'pad'}{input:field 'not-a-field', rules='no-such-rule'/}{/widget:highlight}",
      [ 'pad-highlight', 'not-a-field', 'no-such-rule' ] ],
    'image renderers escape labels' => [
      "{image:emoji 'rocket', label='<Rocket>'/}{image:country 'NL', name/}",
      [ '&lt;Rocket&gt;', 'Netherlands' ] ],
    'shadow root content renders once' => [
      "{widget:shadow}{pad:echo 'Inside'/}{/widget:shadow}",
      [ '<template shadowrootmode="open">Inside</template>' ] ],
  ];

  foreach ( $cases as $name => [ $source, $expected ] ) {
    $process = proc_open ( [ PHP_BINARY, "$root/editors/render.php", 'test', 'index', '--source=-' ],
                          [ [ 'pipe', 'r' ], [ 'pipe', 'w' ], [ 'pipe', 'w' ] ], $pipes, $root );
    if ( ! is_resource ( $process ) ) throw new RuntimeException ( 'Cannot start the renderer' );
    fwrite ( $pipes [0], $source );
    fclose ( $pipes [0] );
    $output = stream_get_contents ( $pipes [1] ); fclose ( $pipes [1] );
    $error = stream_get_contents ( $pipes [2] ); fclose ( $pipes [2] );
    if ( proc_close ( $process ) !== 0 ) throw new RuntimeException ( "$name: $output $error" );
    foreach ( $expected as $part )
      if ( ! str_contains ( $output, $part ) ) throw new RuntimeException ( "$name: missing $part in $output" );
  }

  echo count ( $cases ) . " rendering migration boundary checks passed\n";

?>
