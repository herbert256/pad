# distributions: gamma

Shape/scale convention: mean shape × scale, variance shape × scale². shape 0.01..10000 and scale 1e-150..1e150. Marsaglia–Tsang rejection is limited to 128 attempts; every uniform draw consumes the shared ten-million-work budget. Underflow may yield zero; nonfinite output fails. The supplied seed restores the prior fake stream.

Runnable example: `apps/tags/examples/distribution/gamma.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-gamma.php`.
