# watermark

Creates a deterministic local PNG derivative with exactly one of `text` (1..200 supported font characters) or `mark` (application-owned local image, at most 4 MP). Reuses Noto Sans coverage checks and the ordinary contained image source, orientation, resizing and atomic cache pipeline.

Options: required `alt`, width 32..2000 (600), height 0..2000 (0 means automatic), opacity 0..1 (.5), scale .05..1 (.25 of available width), position top-left/top-right/bottom-left/bottom-right/center. Final images have at least 20px sides and at most 2 MP. Long text scales to the stamp width; transparency is composited with source-over. Cache identity includes both source contents, font hash, text, position, opacity, scale, crop, orientation and output size.

`{image:watermark "photos/avatar-demo.jpg", text="PAD preview", alt="Marked harbour photograph"/}`. This visible mark does not prevent tampering.
