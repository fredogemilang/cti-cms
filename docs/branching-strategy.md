# Branching Strategy

> **Repo:** `github.com/fredogemilang/cti-cms`

## Structure

```
cti-cms/
├── 🌿 main              ← Clean core CMS
├── 🌿 project/cdt        ← CDT deployment
└── 🌿 project/ofis       ← OFIS deployment (next)
```

## What Goes Where

| Branch | Contains |
|--------|----------|
| `main` | `app/`, `plugins/posts/`, `plugins/google-site-kit/`, `themes/default/`, `config/`, `database/`, `routes/` |
| `project/cdt` | `main` + `themes/cdt/` + `plugins/youtube/` |
| `project/ofis` | `main` + `themes/ofis/` |

## Flow: Start New Project

```bash
git checkout main
git checkout -b project/namaproject
# → clean core ready to develop
# → create themes/namaproject/
# → create project-specific plugins if needed
```

## Flow: Improve Core While on Project Branch

### Option A: Commit to main, merge to project (recommended)

```bash
# Fix core bug
git checkout main
# edit app/Helpers.php
git commit -m "[core] fix: resolve_block_asset caching"
git push origin main

# Merge to project branch
git checkout project/cdt
git merge main
# → get core fix, CDT theme stays safe
```

### Option B: Commit on project branch, cherry-pick to main

```bash
# While developing CDT, found core improvement
git checkout project/cdt
# edit core file
git commit -m "[core] feat: add queue batching to FormSubmission"

# Back to main, cherry-pick
git checkout main
git cherry-pick <commit-hash>
git push origin main
```

## Decision: Core vs Project

| Question | Answer |
|----------|--------|
| Useful for ALL projects? | Commit to `main` |
| Project-specific? | Commit on project branch |
| Unsure? | Commit on project branch first, cherry-pick later if other projects need it |

## Plugin Rules

| Plugin | Location | Reason |
|--------|----------|--------|
| `posts/` | `main` | Generic blog, all projects need |
| `google-site-kit/` | `main` | Analytics/Tag Manager, all projects need |
| `youtube/` | `project/cdt` only | CDT-specific |

New plugin: develop on project branch first. If other projects need it → cherry-pick to `main`.

## Theme Rules

- `themes/default/` → on `main`, minimal theme
- `themes/cdt/` → on `project/cdt` (**MUST un-ignore `/themes/cdt` in `.gitignore` on `project/cdt` branch**)
- `themes/ofis/` → on `project/ofis` (**MUST un-ignore `/themes/ofis` in `.gitignore` on `project/ofis` branch**)
- NEVER hardcode path to specific theme in core code

## ⚠️ Server Deployment & Git Pull Protocol

To prevent untracked/missing files issues:

### 1. `.gitignore` Rule per Branch
- `main`: Can ignore `/themes/cdt` & `/themes/ofis` to keep core clean
- `project/{name}`: **DO NOT** ignore `/themes/{name}` in `.gitignore`. All theme views, partials, and assets MUST be 100% tracked on the project branch

### 2. Don't Edit/Create Views Directly on Server Without Git
- NEVER create or edit `.blade.php` files or theme files directly on the hosting server without immediately committing and pushing to the Git repository

### 3. Server Update & Sync Procedure (Root User)
If `git pull` / `git reset` is run as `root` on cPanel server, you **MUST** fix file ownership afterward to prevent PHP/LiteSpeed permission errors:

```bash
cd /home/<cpanel_user>/<domain>/
git fetch origin
git reset --hard origin/project/<name>
chown -R <cpanel_user>:<cpanel_user> /home/<cpanel_user>/<domain>/
```
