# Publishing architecture

Publishing validates the draft tree, creates an immutable publish snapshot, renders it through the same registry used by preview, stores derived HTML and CSS, updates the active publish pointer, and invalidates tenant-specific cache keys.

Draft edits never mutate an existing published version. Rollback changes the active version pointer. Compilation can move to a queued job as pages grow; a database transaction still protects publication state.
