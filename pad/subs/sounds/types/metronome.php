<?php

  // metronome - A bounded accented click track using the shared deterministic PCM/WAV synthesizer. `bpm` 30..300 (120), `meter` integer beats per measure 1..12 (4), `measures` 1..4 (1), `accent` downbeat gain 0..1 (1), `level` peak 0..1 (.4; zero is silent). Non-downbeats use gain .6 and lower pitch. Total duration, including the final rest, must be at most the existing four-second sound budget.
  //
  // `{sound:metronome bpm=120, meter=4, measures=2/}`. Native audio controls provide play/pause/mute and no autoplay; the source is an ordinary same-origin WAV route with a configuration identity, so no inline JavaScript or data: media allowance is needed. PHP `padMetronomeRecipe(...)` returns the settled recipe for sample/export parity.
  //
  // Common sound options `button`, `target`, `on`, `volume` and `download` retain their shared semantics. Buttons and event bindings synthesize the configured recipe under its own identity; the WAV download and no-script link use that same configuration. Native audio controls remain available for play/pause/mute without JavaScript. `volume` controls button/binding playback, as for other sound kinds; native audio has its own volume control.

include_once PAD.'subs/sounds/_lib/metronome.php';
return padMetronomeRaw();
