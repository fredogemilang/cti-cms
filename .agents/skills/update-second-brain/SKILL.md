---
name: update-second-brain
description: Update the Obsidian second brain vault with new knowledge from the current session. Trigger at end of significant work sessions, after architectural changes, or when user says "update second brain".
---

# Update Second Brain

## Vault Location
`C:\Users\fredo\Documents\Obsidian Vault\altia\CTI-CMS\`

## When to Trigger
1. **End of significant session** — multiple files changed, new features built, bugs fixed
2. **Architectural change** — new system, modified core pattern, new convention
3. **New gotcha discovered** — bug, pitfall, or lesson learned
4. **User explicitly asks** — "update second brain", "catat ini", "simpan knowledge"

Do NOT trigger for trivial changes (typo fix, single CSS tweak, quick answer).

## What to Update

### 1. Session Log (ALWAYS for significant sessions)
**File:** `Session {YYYY-MM-DD}.md`
- If file exists for today → **append** new section at bottom
- If not → create new file

**Template:**
```markdown
---
title: "Session {YYYY-MM-DD}"
date: {YYYY-MM-DD}
tags: [session, {relevant-tags}]
---

# Session {YYYY-MM-DD} — {Brief Title}

## Work Completed

### 1. {Feature/Fix Name}
- What was done (1-3 bullet points)
- Key decisions made

## Files Changed
| File | Change |
|------|--------|
| `relative/path` | Brief description |
```

### 2. Gotchas (When a pitfall/lesson is discovered)
**File:** `Gotchas.md`
- **Append** at bottom with next number
- Format: `## G{N}. {Title} ({YYYY-MM-DD})`
- Keep description to 1-3 lines max

### 3. Architecture Docs (When system behavior changes)
**Files:** `Arch - {System Name}.md`
- **Update** existing section, don't duplicate
- If entirely new system → create new `Arch - {Name}.md`
- Add to MOC (`🧠-MOC.md`) if new file created

### 4. Pattern Docs (When new reusable pattern established)
**Files:** `Pattern - {Name}.md`
- Only create if pattern will be reused across sessions
- Add to MOC if new file created

### 5. Quick Context (Rarely — only for fundamental changes)
**File:** `Quick Context.md`
- Update only if a core convention changes (e.g., new translation system, new DB pattern)
- This file should stay under 60 lines

## Rules
1. **Be terse** — tables > paragraphs, code > prose
2. **Don't duplicate** — if info is in AGENTS.md, don't repeat in vault
3. **Date everything** — every entry should have a date
4. **Flat structure** — no subdirectories, use prefixed filenames
5. **Token-efficient** — an AI reading this should get max context in min tokens
6. **Indonesian OK** — mixed Bahasa/English is fine, match the user's style
