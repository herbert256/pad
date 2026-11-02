# metronome

A bounded accented click track using the shared deterministic PCM/WAV synthesizer. `bpm` 30..300 (120), `meter` integer beats per measure 1..12 (4), `measures` 1..4 (1), `accent` downbeat gain 0..1 (1), `level` peak 0..1 (.4; zero is silent). Non-downbeats use gain .6 and lower pitch. Total duration, including the final rest, must be at most the existing four-second sound budget.

`{sound:metronome bpm=120, meter=4, measures=2/}`. Native audio controls provide play/pause/mute and no autoplay; the source is an ordinary same-origin WAV route with a configuration identity, so no inline JavaScript or data: media allowance is needed. PHP `padMetronomeRecipe(...)` returns the settled recipe for sample/export parity.
