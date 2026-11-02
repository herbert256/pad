# toys: lorenz

`{toy:lorenz sigma=10, rho=28, beta=2.6666667, dt=0.005, projection='xz', seed=7, paused/}`
integrates the Lorenz equations using fourth-order Runge–Kutta. The displayed timestep
is explicit; five fixed integration steps run per animation frame. Play/Pause,
single-step, reset, projection and timestep controls share the toy lifecycle. Reset
restores the same seeded initial state. Trail is bounded to 100–6000 points; nonfinite
or runaway state pauses with a numerical-divergence message. This is an illustrative
numerical model, not a calibrated weather prediction. Reduced motion starts paused.
