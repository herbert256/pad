<?php

  // The sounds subsystem: short sounds for a page - a chime when something is saved, a pop,
  // a coin, a drum - made from a recipe, with no audio file anywhere.
  //
  //   {sound:chime/}                              a button that plays it, named Chime
  //   {sound:chime button='Play'/}             the tag; button= the button's label
  //   {sound:pop on='click', target='#save'/}     no button: played when #save is clicked
  //   <button data-pad-sound="pop">Pop</button>   any element, once the script is on the page
  //   {sound:chime download/}                     with a link to the sound as a WAV file
  //   {widget:toast 'Saved', sound='success'/}           a toast that sounds as it shows
  //
  // A sound is a file of subs/sounds/types/ that returns its recipe, a PHP array - the files
  // there are the sounds there are (padSoundKinds, lib/sound.php). A recipe is voices played
  // together, each an oscillator - sine, square, saw, triangle - or noise, with an envelope
  // (attack, decay to a sustain level, release), a pitch that sweeps (to=), wobbles (vibrato)
  // or steps through a short run of notes (notes=, step=), and a filter - lowpass, highpass
  // or bandpass - whose cutoff may sweep too. The keys of a voice:
  //
  //   wave     sine, square, saw, triangle or noise
  //   note     the pitch: a note name - A4, C#5, Bb3 - or a number of Hz; of noise, the rate
  //            it is held at for a coarse, pitched noise, white without it
  //   notes    a run of notes instead, 'C5 E5 G5', a - a step of rest; step= the seconds from
  //            one to the next (its length without it); of noise, x a hit of white noise -
  //            'x x - x', a run of claps
  //   to      the pitch it sweeps to, over glide= seconds (its length without it); bend= the
  //            same as semitones, for every note of a run
  //   start    when it starts, in seconds; length= how long a note lasts
  //   attack, decay, sustain, release   the envelope - seconds, and sustain a level 0 to 1
  //   gain     its share of the mix; duty= the width of a square's pulse; vibrato= [ Hz,
  //            semitones ]; filter= [ type, Hz, to Hz, q ]
  //
  // The recipe as a whole: title= its name, level= the peak it is scaled to (0.8), seed=
  // the start of its noise.
  //
  // One recipe, two renderings that are the same: the server reads the recipe and settles
  // it - every note to its Hz, every default filled in (padSoundRecipe) - and both renderers
  // take that settled recipe, sample for sample the same arithmetic: padSoundRender here, for
  // padSoundWav's WAV file, and www/assets/sounds/sound.js in the browser, which plays the samples it
  // made through WebAudio - an AudioBuffer at 22050 samples a second, one buffer per sound,
  // made on its first play. So the WAV file is what the page plays, and the stored answers
  // can hold its md5. The noise comes from a seeded xorshift, so it is the same on every
  // render too. WebAudio's own oscillators and filters are not used: their band limiting and
  // filter curves are the browser's own, and the two would not be the same sound.
  //
  // The browser plays only after the visitor has done something on the page - a click, a
  // key - as browsers allow audio: the AudioContext is made, or resumed, on the first such
  // gesture. A sound bound to hover or focus, or a toast's sound on a page just loaded, is
  // silent before that.
  //
  // padSoundRecipe     a kind's recipe, settled
  // padSoundRender     the recipe as 16-bit samples
  // padSoundWavBytes   the samples as a WAV file
  // padSoundAnswer     the WAV as the answer of the request
  // padSoundTag        what {sound:chime} writes: the button, the binding, the download link
  // padSoundScript     the runtime and every recipe, once per page with the CSP nonce
  // padSoundStyle      the button's look, once per page

  const PAD_SOUND_RATE    = 22050;
  const PAD_SOUND_LONGEST = 4;
  const PAD_SOUND_WAVES   = [ 'sine', 'square', 'saw', 'triangle', 'noise' ];
  const PAD_SOUND_FILTERS = [ 'lowpass', 'highpass', 'bandpass' ];

  // The events a sound is played on - on= of {sound:chime}, data-pad-sound-on of an element - and
  // the DOM event each listens to; hover is the pointer coming onto the element.

  const PAD_SOUND_EVENTS  = [ 'click' => 'click', 'pointerdown' => 'pointerdown', 'hover' => 'pointerover',
                              'focus' => 'focusin', 'change' => 'change', 'input' => 'input', 'submit' => 'submit' ];

  // ------------------------------------------------------------------------------------
  // The recipe.
  // ------------------------------------------------------------------------------------

  function padSoundRecipe ( $kind ) {

    static $memo = [];

    return $memo [$kind] ??= padSoundSettle ( $kind, padSoundRaw ( $kind ) );

  }

  // The kind's file, run in a function of its own: its variables are its locals.

  function padSoundRaw ( $padSoundKind ) {

    $padSoundRecipe = include PAD . "subs/sounds/types/$padSoundKind.php";

    return is_array ( $padSoundRecipe ) ? $padSoundRecipe : [];

  }

  // The recipe settled: every voice with all its keys, a note as Hz, a run of notes as their
  // start, pitch and the pitch each sweeps to, the seed of its noise - what both renderers
  // read. A recipe the subsystem cannot read is an error naming the kind.

  function padSoundSettle ( $kind, $raw ) {

    $voices = [];
    $length = 0.0;
    $seed   = (int) ( $raw ['seed'] ?? 1 );

    foreach ( array_values ( $raw ['voices'] ?? [] ) as $k => $one ) {

      $wave = $one ['wave'] ?? 'sine';

      if ( ! in_array ( $wave, PAD_SOUND_WAVES, TRUE ) )
        padError ( "the sound '$kind' has a voice of no wave '" . padMakeSafe ( (string) $wave, 20 ) . "' - " . implode ( ', ', PAD_SOUND_WAVES ) );

      $note   = (float) ( $one ['length'] ?? 0.2 );
      $note   = min ( max ( $note, 0.001 ), PAD_SOUND_LONGEST );
      $notes  = [];
      $bend   = (float) ( $one ['bend'] ?? 0 );

      if ( isset ( $one ['notes'] ) ) {

        $step = (float) ( $one ['step'] ?? $note );
        $at   = 0.0;

        foreach ( preg_split ( '/[\s,]+/', trim ( (string) $one ['notes'] ) ) as $name ) {
          if ( $name !== '-' ) {
            if ( $name === 'x' and $wave != 'noise' )
              padError ( "the sound '$kind' has an x in the notes of a $wave voice - x is a hit of white noise, in the notes of a noise voice" );
            $hz      = ( $name === 'x' ) ? 0.0 : padSoundHz ( $kind, $name );
            $notes[] = [ $at, $hz, $bend ? $hz * pow ( 2, $bend / 12 ) : $hz ];
          }
          $at += $step;
        }

      } else {

        $hz      = isset ( $one ['note'] ) ? padSoundHz ( $kind, $one ['note'] ) : 0.0;
        $to      = isset ( $one ['to'] ) ? padSoundHz ( $kind, $one ['to'] ) : ( $bend ? $hz * pow ( 2, $bend / 12 ) : $hz );
        $notes[] = [ 0.0, $hz, $hz > 0 ? $to : 0.0 ];

      }

      if ( $wave != 'noise' and ! array_filter ( $notes, fn ( $n ) => $n [1] > 0 ) )
        padError ( "the sound '$kind' has a $wave voice without a note" );

      $filter = NULL;

      if ( isset ( $one ['filter'] ) ) {
        list ( $type, $from, $to, $q ) = array_pad ( (array) $one ['filter'], 4, NULL );
        if ( ! in_array ( $type, PAD_SOUND_FILTERS, TRUE ) )
          padError ( "the sound '$kind' has a filter of no type '" . padMakeSafe ( (string) $type, 20 ) . "' - " . implode ( ', ', PAD_SOUND_FILTERS ) );
        $from   = padSoundHz ( $kind, $from ?? 1000 );
        $filter = [ (string) $type, $from, $to === NULL ? $from : padSoundHz ( $kind, $to ), (float) ( $q ?? 0.7071 ) ];
      }

      $vibrato = array_map ( 'floatval', array_pad ( (array) ( $one ['vibrato'] ?? [] ), 2, 0 ) );
      $start   = max ( 0.0, (float) ( $one ['start'] ?? 0 ) );
      $span    = end ( $notes ) [0] + $note;

      $voices [] = [
        'wave'    => $wave,
        'start'   => $start,
        'length'  => $note,
        'glide'   => max ( 0.001, (float) ( $one ['glide'] ?? $note ) ),
        'attack'  => max ( 0.0, (float) ( $one ['attack']  ?? 0.005 ) ),
        'decay'   => max ( 0.0, (float) ( $one ['decay']   ?? 0 ) ),
        'sustain' => min ( 1.0, max ( 0.0, (float) ( $one ['sustain'] ?? 1 ) ) ),
        'release' => max ( 0.0, (float) ( $one ['release'] ?? 0.02 ) ),
        'gain'    => (float) ( $one ['gain'] ?? 0.5 ),
        'duty'    => min ( 0.95, max ( 0.05, (float) ( $one ['duty'] ?? 0.5 ) ) ),
        'vibrato' => [ $vibrato [0], $vibrato [1] ],
        'filter'  => $filter,
        'seed'    => padSoundSeed ( $seed, $k ),
        'notes'   => $notes ];

      $length = max ( $length, $start + $span );

    }

    if ( ! $voices )
      padError ( "the sound '$kind' has no voices" );

    return [ 'kind'   => $kind,
             'title'  => (string) ( $raw ['title'] ?? ucfirst ( $kind ) ),
             'level'  => min ( 1.0, max ( 0.05, (float) ( $raw ['level'] ?? 0.8 ) ) ),
             'length' => min ( $length, (float) PAD_SOUND_LONGEST ),
             'voices' => $voices ];

  }

  // The seed of a voice's noise: the recipe's, turned by the voice's place - an unsigned
  // 32-bit number that is never 0, which xorshift would keep at 0.

  function padSoundSeed ( $seed, $k ) {

    $value = ( $seed + ( $k + 1 ) * 2654435761 ) % 4294967296;

    return $value ?: 2463534242;

  }

  // A pitch: a number of Hz, or a note - a letter, a sharp (#) or a flat (b), an octave -
  // in equal temperament with A4 at 440.

  function padSoundHz ( $kind, $value ) {

    if ( is_int ( $value ) or is_float ( $value ) or ( is_string ( $value ) and is_numeric ( $value ) ) )
      return (float) $value;

    if ( is_string ( $value ) and preg_match ( '/^([A-Ga-g])([#b]?)(-?\d)$/D', $value, $m ) ) {
      $semi = [ 'c' => 0, 'd' => 2, 'e' => 4, 'f' => 5, 'g' => 7, 'a' => 9, 'b' => 11 ] [ strtolower ( $m [1] ) ];
      $midi = 12 * ( (int) $m [3] + 1 ) + $semi + ( $m [2] == '#' ? 1 : ( $m [2] == 'b' ? -1 : 0 ) );
      return 440 * pow ( 2, ( $midi - 69 ) / 12 );
    }

    padError ( "the sound '$kind' has no note '" . padMakeSafe ( is_scalar ( $value ) ? (string) $value : '', 20 ) . "' - a note like A4, C#5 or Bb3, or a number of Hz" );

    return 440.0;

  }

  // ------------------------------------------------------------------------------------
  // The rendering - www/assets/sounds/sound.js does the same, step for step: change one, change both.
  // ------------------------------------------------------------------------------------

  // The settled recipe as samples: every voice into its own buffer, filtered, added to the
  // mix at its start; the mix scaled so its loudest sample is the recipe's level, then each
  // sample a whole number from -32767 to 32767.

  function padSoundRender ( $recipe ) {

    $rate  = PAD_SOUND_RATE;
    $count = (int) floor ( $recipe ['length'] * $rate + 0.5 );
    $mix   = array_fill ( 0, max ( 1, $count ), 0.0 );

    foreach ( $recipe ['voices'] as $voice )
      padSoundVoice ( $voice, $mix, $rate );

    $peak = 0.0;

    foreach ( $mix as $value )
      if ( abs ( $value ) > $peak )
        $peak = abs ( $value );

    $scale   = ( $peak > 0 ) ? $recipe ['level'] / $peak : 0.0;
    $samples = [];

    foreach ( $mix as $value ) {
      $q = floor ( $value * $scale * 32767 + 0.5 );
      $samples [] = (int) ( $q > 32767 ? 32767 : ( $q < -32767 ? -32767 : $q ) );
    }

    return $samples;

  }

  function padSoundVoice ( $voice, &$mix, $rate ) {

    $length  = $voice ['length'];
    $notes   = $voice ['notes'];
    $span    = $notes [ count ( $notes ) - 1 ] [0] + $length;
    $size    = (int) floor ( $span * $rate + 0.5 );
    $buffer  = array_fill ( 0, max ( 1, $size ), 0.0 );
    $state   = $voice ['seed'];
    $wave    = $voice ['wave'];
    $glide   = $voice ['glide'];
    $attack  = $voice ['attack'];
    $decay   = $voice ['decay'];
    $sustain = $voice ['sustain'];
    $release = min ( $voice ['release'], $length );
    $gain    = $voice ['gain'];
    $duty    = $voice ['duty'];
    $vibRate = $voice ['vibrato'] [0];
    $vibDeep = $voice ['vibrato'] [1];
    $twoPi   = 2 * M_PI;

    foreach ( $notes as list ( $at, $from, $to ) ) {

      $first = (int) floor ( $at * $rate + 0.5 );
      $count = (int) floor ( $length * $rate + 0.5 );
      $phase = 0.0;
      $hold  = 0.0;
      $held  = 1.0;

      for ( $i = 0; $i < $count and $first + $i < $size; $i++ ) {

        $t = $i / $rate;

        // The pitch: swept from the note to its target, the vibrato on top.

        $f = $from;

        if ( $to != $from and $from > 0 )
          $f = $from * pow ( $to / $from, min ( $t / $glide, 1.0 ) );

        if ( $vibDeep != 0 )
          $f = $f * pow ( 2, $vibDeep / 12 * sin ( $twoPi * $vibRate * $t ) );

        // The oscillator - a square of a pulse narrower than half moved back to the middle,
        // its mean 0, as a pulse wave off the middle pushes the whole sound to one side.

        if ( $wave == 'sine' )
          $x = sin ( $twoPi * $phase );
        elseif ( $wave == 'square' )
          $x = ( ( $phase < $duty ) ? 1.0 : -1.0 ) - ( 2 * $duty - 1 );
        elseif ( $wave == 'saw' )
          $x = 2 * $phase - 1;
        elseif ( $wave == 'triangle' )
          $x = ( $phase < 0.5 ) ? 4 * $phase - 1 : 3 - 4 * $phase;
        elseif ( $from <= 0 ) {
          $state ^= ( $state << 13 ) & 0xFFFFFFFF;
          $state ^= $state >> 17;
          $state ^= ( $state << 5 ) & 0xFFFFFFFF;
          $x = $state / 2147483648 - 1;
        } else {
          if ( $held >= 1 ) {
            $state ^= ( $state << 13 ) & 0xFFFFFFFF;
            $state ^= $state >> 17;
            $state ^= ( $state << 5 ) & 0xFFFFFFFF;
            $hold = $state / 2147483648 - 1;
            $held = $held - floor ( $held );
          }
          $x    = $hold;
          $held = $held + $f / $rate;
        }

        $phase = $phase + $f / $rate;
        $phase = $phase - floor ( $phase );

        // The envelope: up over the attack, down over the decay to the sustain level - a
        // curve, as a struck sound dies - and to nothing over the release at the end.

        if ( $t < $attack )
          $e = $t / $attack;
        elseif ( $decay > 0 and $t < $attack + $decay ) {
          $d = 1 - ( $t - $attack ) / $decay;
          $e = $sustain + ( 1 - $sustain ) * $d * $d;
        } else
          $e = $sustain;

        if ( $release > 0 and $t > $length - $release )
          $e = $e * max ( 0.0, ( $length - $t ) / $release );

        $buffer [ $first + $i ] += $x * $e * $gain;

      }

    }

    if ( $voice ['filter'] )
      padSoundFilter ( $buffer, $voice ['filter'], $rate );

    $offset = (int) floor ( $voice ['start'] * $rate + 0.5 );
    $count  = count ( $mix );

    foreach ( $buffer as $i => $value )
      if ( $offset + $i < $count )
        $mix [ $offset + $i ] += $value;

  }

  // A biquad of the Audio EQ Cookbook (R. Bristow-Johnson), its cutoff swept from the first
  // frequency to the second over the voice - the coefficients made again for every sample
  // of a sweep, once for a cutoff that stays.

  function padSoundFilter ( &$buffer, $filter, $rate ) {

    list ( $type, $from, $to, $q ) = $filter;

    $size = count ( $buffer );
    $x1 = $x2 = $y1 = $y2 = 0.0;
    $b0 = $b1 = $b2 = $a1 = $a2 = 0.0;

    for ( $i = 0; $i < $size; $i++ ) {

      if ( $i == 0 or $to != $from ) {

        $fc = ( $to != $from ) ? $from * pow ( $to / $from, $i / $size ) : $from;
        $fc = min ( max ( $fc, 20.0 ), $rate * 0.45 );

        $w     = 2 * M_PI * $fc / $rate;
        $cs    = cos ( $w );
        $alpha = sin ( $w ) / ( 2 * $q );
        $a0    = 1 + $alpha;

        if ( $type == 'lowpass' ) {
          $b0 = ( 1 - $cs ) / 2;
          $b1 = 1 - $cs;
          $b2 = $b0;
        } elseif ( $type == 'highpass' ) {
          $b0 = ( 1 + $cs ) / 2;
          $b1 = - ( 1 + $cs );
          $b2 = $b0;
        } else {
          $b0 = $alpha;
          $b1 = 0.0;
          $b2 = - $alpha;
        }

        $b0 = $b0 / $a0;
        $b1 = $b1 / $a0;
        $b2 = $b2 / $a0;
        $a1 = -2 * $cs / $a0;
        $a2 = ( 1 - $alpha ) / $a0;

      }

      $x = $buffer [$i];
      $y = $b0 * $x + $b1 * $x1 + $b2 * $x2 - $a1 * $y1 - $a2 * $y2;

      $x2 = $x1;
      $x1 = $x;
      $y2 = $y1;
      $y1 = $y;

      $buffer [$i] = $y;

    }

  }

  // The samples as a WAV file: the RIFF header of 16-bit PCM, one channel, then the samples,
  // little-endian.

  function padSoundWavBytes ( $recipe ) {

    $data = pack ( 'v*', ...array_map ( fn ( $s ) => $s & 0xFFFF, padSoundRender ( $recipe ) ) );
    $rate = PAD_SOUND_RATE;

    return 'RIFF' . pack ( 'V', 36 + strlen ( $data ) ) . 'WAVE'
         . 'fmt ' . pack ( 'VvvVVvv', 16, 1, 1, $rate, $rate * 2, 2, 16 )
         . 'data' . pack ( 'V', strlen ( $data ) ) . $data;

  }

  // The WAV as the answer of this request - the page asked again with &padSound=<kind> by
  // the download link of a {sound:chime}, or a page's own PHP through padSoundSend: the headers
  // set, the rest of the page left out, as padUploadSend sends a file.

  function padSoundAnswer ( $kind, $download = FALSE, $recipe = NULL ) {

    global $padContentType, $padGzip, $padLen, $padCache, $padWebEtag304;

    if ( headers_sent () )
      return padError ( "the sound '$kind' cannot be sent - the headers have gone out already, and a sound is the whole answer of a page" );

    $wav = padSoundWavBytes ( $recipe ?? padSoundRecipe ( $kind ) );

    padCloseSession ();
    padEmptyBuffers ( $padSoundIgnored );

    $padContentType = 'audio/wav';
    $padGzip        = FALSE;
    $padLen         = 0;
    $padCache       = FALSE;
    $padWebEtag304  = FALSE;

    padWebHeaders ( 200 );
    padHeader ( 'Content-Disposition: ' . ( $download ? 'attachment' : 'inline' ) . "; filename=\"$kind.wav\"" );
    padHeader ( 'Content-Length: ' . strlen ( $wav ) );
    padHeader ( 'Cache-Control: private, max-age=3600' );

    $GLOBALS ['padSent'] = TRUE;

    while ( ob_get_level () )
      ob_end_clean ();

    echo $wav;

    flush ();

    padExit ( 200 );

  }

  // ------------------------------------------------------------------------------------
  // The tag.
  // ------------------------------------------------------------------------------------

  // What {sound:chime} writes, as the options ask: a button that plays the sound - its label
  // button=, else the sound's title - unless target= binds the sound to elements of the
  // page instead (on= the event, click unless told); with download a link to the WAV file
  // beside it; on= the event the button plays on as well, click unless told. volume= the
  // loudness of the playing, 0 to 100. Without script the button is hidden and a link to the
  // WAV stands in its place, which the browser plays in a player of its own - a link, as an
  // <audio> inside the inline span is taken apart by tidy.

  function padSoundTag ( $kind ) {

    global $padCheckSyntax;

    $recipe = padSoundRecipe ( $kind );
    $target = padSoundText ( 'target' );
    $button = padTagParm ( 'button', NULL );
    $on     = padSoundChoice ( 'on', 'click', array_keys ( PAD_SOUND_EVENTS ) );

    // Without target= the sound is a button of its own, which only click, pointerdown, hover
    // and focus reach: it is never submitted - the form is - nor changed nor typed in, and
    // a button that played on those never played. Named under the strict check, a click
    // otherwise.

    if ( $target === '' and in_array ( $on, [ 'change', 'input', 'submit' ], TRUE ) ) {
      if ( $padCheckSyntax )
        padError ( "the {sound:chime} plays on '$on' only with target= - a button of its own is never submitted, changed nor typed in: target='"
                   . ( $on == 'submit' ? 'form' : 'input' ) . "'" );
      $on = 'click';
    }
    $volume = padSoundVolume ();
    $file   = padTagParm ( 'download', FALSE );
    $html   = '';
    $more   = ( $volume < 100 ) ? ' data-pad-sound-volume="' . ( $volume / 100 ) . '"' : '';
    $url    = padSoundUrl ( $kind );

    if ( $target !== '' )
      $html .= '<span class="pad-sound-bind" hidden data-pad-sound-bind="' . $kind . '" data-pad-sound-on="' . $on
             . '" data-pad-sound-target="' . padWidgetAttr ( $target ) . "\"$more></span>";

    if ( $target === '' or $button !== NULL ) {

      $label = is_scalar ( $button ) ? trim ( padParmText ( (string) $button ) ) : '';
      $label = ( $label !== '' and $button !== TRUE ) ? $label : $recipe ['title'];

      $html .= '<button type="button" class="pad-sound-button" data-pad-sound="' . $kind . '"'
             . ( ( $target === '' and $on != 'click' ) ? " data-pad-sound-on=\"$on\"" : '' ) . "$more>"
             . '<svg class="pad-sound-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M4 9h4l5-4v14l-5-4H4z"/>'
             . '<path d="M16.5 8.5a5 5 0 0 1 0 7M19 6a8.5 8.5 0 0 1 0 12" fill="none"/></svg>'
             . padWidgetAttr ( $label ) . '</button>'
             . "<noscript><a class=\"pad-sound-link\" href=\"$url\" type=\"audio/wav\">" . padWidgetAttr ( $label ) . '</a></noscript>';

    }

    if ( $file !== FALSE and $file !== '' and $file !== 0 and $file !== '0' ) {
      $text  = ( is_string ( $file ) and trim ( padParmText ( $file ) ) !== '' ) ? trim ( padParmText ( $file ) ) : "$kind.wav";
      $html .= " <a class=\"pad-sound-download\" href=\"$url\" type=\"audio/wav\" download=\"$kind.wav\">" . padWidgetAttr ( $text ) . '</a>';
    }

    // A {pad:cache} section round a link to the WAV is not kept, as one round a {pad:pdf} is not
    // (padFragmentKeepNot): on a hit the tag did not run, and the page asked with &padSound
    // answered its HTML - and the link holds this request's own values.

    if ( str_contains ( $html, "href=\"$url\"" ) )
      padFragmentKeepNot ();

    return padSoundStyle () . padProtect ( "<span class=\"pad-sound pad-sound-$kind\">$html</span>" ) . padSoundScript ();

  }

  // This page asked again for the WAV of a sound: its route and its own request values, as
  // {pad:pdf preview} links to its document (padPdfLinks), with padSound=<kind>.

  function padSoundUrl ( $kind ) {

    global $padPage, $padPageAsked;

    $asked = (string) ( $padPageAsked ?? '' ) !== '' ? (string) $padPageAsked : (string) $padPage;
    $vars  = [];

    foreach ( $_GET as $key => $value )
      if ( ( (string) $key !== $asked or $value !== '' ) and ! str_starts_with ( (string) $key, 'pad' ) )
        $vars [$key] = $value;

    return htmlspecialchars ( padUrl ( '', $vars + [ 'padSound' => $kind ] ), ENT_QUOTES );

  }

  // A text option - target= - read as the characters it holds.

  function padSoundText ( $option ) {

    $value = padTagParm ( $option, '' );

    return is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

  }

  function padSoundChoice ( $option, $default, $choices ) {

    global $padCheckSyntax;

    $value = padTagParm ( $option, $default );
    $value = is_scalar ( $value ) ? strtolower ( trim ( padParmText ( (string) $value ) ) ) : '';

    if ( in_array ( $value, $choices, TRUE ) )
      return $value;

    if ( $padCheckSyntax )
      padError ( "the {sound:chime} has no $option '" . padMakeSafe ( $value, 20 ) . "' - " . implode ( ', ', $choices ) );

    return $default;

  }

  // volume=, a whole number 0 to 100 - the share of the sound's own level it plays at.

  function padSoundVolume () {

    global $padCheckSyntax;

    $value = padTagParm ( 'volume', 100 );
    $text  = is_scalar ( $value ) ? trim ( padParmText ( (string) $value ) ) : '';

    if ( preg_match ( '/^\d{1,3}$/D', $text ) and (int) $text <= 100 )
      return (int) $text;

    if ( $padCheckSyntax )
      padError ( "the {sound:chime} has no volume '" . padMakeSafe ( $text, 20 ) . "' - a whole number from 0 to 100" );

    return 100;

  }

  // ------------------------------------------------------------------------------------
  // The script and the style, once per page.
  // ------------------------------------------------------------------------------------

  // The runtime, www/assets/sounds/sound.js, and every recipe there is, settled - so a data-pad-sound
  // of any kind plays once a {sound:chime} or a toast with a sound has written the script.

  function padSoundScript () {

    if ( ! padWidgetOnce ( 'script:sound' ) )
      return '';

    $recipes = [];

    foreach ( padSoundKinds () as $kind )
      $recipes [$kind] = padSoundRecipe ( $kind );

    $json = json_encode ( $recipes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_PRESERVE_ZERO_FRACTION );

    // The nonce as padWidgetScript writes it, whose once is asked for above.

    global $padCsp;

    $nonce = ( is_string ( $padCsp ?? NULL ) and str_contains ( $padCsp, "'nonce'" ) )
           ? ' nonce="' . htmlspecialchars ( padNonce (), ENT_QUOTES ) . '"' : '';

    // Linked, the runtime is a <script src> of www/assets/sounds/ and the recipes a line of
    // their own behind it; written in, one script holds both.

    $runtime = padWidgetFile ( 'sounds/sound.js' );
    $add     = "PadSound.add($json, " . json_encode ( PAD_SOUND_EVENTS ) . ');';

    if ( is_array ( $runtime ) )
      return padProtect ( '<script src="' . htmlspecialchars ( $runtime ['url'], ENT_QUOTES ) . "\"$nonce></script>"
                          . "<script$nonce>$add</script>" );

    return padProtect ( "<script$nonce>$runtime\n$add</script>" );

  }

  function padSoundStyle () {

    return padWidgetStyle ( 'sound',
      [ 'text'   => [ '#1f1f1d', '#ecebe6' ],
        'line'   => [ '#cfcec8', '#4a4a46' ],
        'face'   => [ '#ffffff', '#242422' ],
        'accent' => [ '#2a78d6', '#5598e7' ] ],
      padWidgetFile ( 'sounds/sound.css' ) );

  }

?>
