# Dorra Agent Working Package

1. Copy `AGENTS.md`, `docs/` and `START_PROMPT.md` into the **root of the new standalone Laravel repository**.
2. Paste the body of `START_PROMPT.md` as the first instruction to your coding agent.
3. The agent edits and verifies **one phase**, updates `docs/PLAN.md` + the active spec, and stops at `Awaiting Commit`.
4. Review the code and actual test outputs. **You** run `git add ...` and `git commit -m "..."`; confirm the commit to the agent.
5. The agent checks the commit (when possible), marks previous phase `Done`, and starts the next phase **only on your approval**.

If a task is blocked, leave relevant checkbox unchecked, explain the missing evidence, and do not silently continue. Project deliverables after implementation include `README.md` and `docs/performance.md`; this starter package contains the **agent workflow**, not the Laravel application.
