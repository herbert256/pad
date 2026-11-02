# Movement games and signal toys

```pad
{game:2048 seed=17/}
{game:breakout/}
{toy:fourier signal='square', harmonics=8, paused/}
{toy:waveinterference frequency=1, phase=180, separation=3, paused/}
```

2048 uses a fixed 4×4 board, one merge per tile per move, 90% twos and 10% fours on empty cells after successful moves only. `seed` makes initial tiles, subsequent spawns and reset reproducible. Arrow keys, swipe and buttons play; reaching 2048 wins, and a full board without a merge loses. Separate instances keep independent random streams. There is no continuous motion.

Breakout has a fixed logical 480×320 field, 32 bricks and three lives. It starts paused, including under reduced motion. Play/Pause (or Space), arrow keys, pointer dragging and buttons control it. Physics advances in 1/120-second steps with a 0.1-second catch-up cap, so frame rates do not change ordinary collisions. Hidden pages pause; returning requires Play. Reset restores the initial state. Removing the game cancels animation and event subscriptions. Canvas display scales responsively without changing physics coordinates.

Fourier samples a sine, square or saw signal 128 times per period. It shows the real DFT spectrum (1–32 harmonics), reconstructed signal and RMS sample error. The fundamental is 1 Hz, horizontal position is seconds and amplitudes are dimensionless. Changing harmonic count changes the reconstruction, rather than merely animating epicycles.

Wave interference sums two ideal coherent point sources with wavelength 2 m in a 12×10 m field. Frequency is 0.2–3 Hz, phase 0–360 degrees, separation 0–8 m. Amplitude colors run blue (−1) through white (0) to red (+1). This is a constant-amplitude phase model without geometric attenuation, boundary reflection or a wave-equation solver. Co-located sources at opposite phase cancel. Both toys use fixed 1/120-second clocks and the common toy lifecycle (visibility suspension, reduced-motion pause, unmount cleanup and deterministic time reset).
