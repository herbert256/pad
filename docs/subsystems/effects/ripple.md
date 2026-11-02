# effects: ripple

`{effect:ripple}<button type='submit'>Save</button>{/effect:ripple}` decorates an
existing native control. Click (including keyboard activation) produces a short
pointer-transparent ring; event propagation, submission and navigation remain native.
At most three rings exist per wrapper; each is removed after 500 ms. Reduced motion
uses a static active outline. The delegated listener also covers remounted fragments.
