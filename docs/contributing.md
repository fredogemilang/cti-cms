# Contributing & PR Workflow

Panduan untuk kontributor eksternal (fork) dan internal. Baca ini **sebelum**
membuka pull request.

## Sync Repository

Repo utama: `github.com/fredogemilang/cti-cms`

```bash
git remote add upstream https://github.com/fredogemilang/cti-cms.git   # kalau belum ada
git fetch upstream

# Selalu buat branch kerja BARU dari upstream (jangan lanjut dari branch lama
# yang sudah direstrukturisasi)
git checkout -b feat/<nama> upstream/project/<client>
```

Setelah sync: `composer dump-autoload` lalu pastikan `php artisan test` hijau.

## Ke Mana Commit / PR per Jenis Perubahan

| Jenis perubahan | Lokasi | Target PR |
|-----------------|--------|-----------|
| Fitur CMS generic (semua klien pakai) | `app/`, `database/`, `docs/`, `routes/`, `resources/views/components/` | `main` |
| Theme / plugin client (mis. xyora) | `themes/{slug}/`, `plugins/{slug}/` | `project/{slug}` |
| Bugfix core | `app/` dll. | `main` (lalu di-merge ke project branch) |

> **Jangan buka PR client-specific ke `main`.** Kalau ragu apakah perubahan
> generic atau client-specific → tulis proposal / discuss dulu, JANGAN langsung
> implementasi di core (Gotcha G11). Detail: `docs/branching-strategy.md`.

## Pre-PR Checklist (wajib lolos semua)

1. `./vendor/bin/pint` — tidak ada perubahan style tersisa
2. `php artisan test` — semua test hijau
3. `php artisan view:cache` — semua Blade view terkompilasi tanpa error
4. **EN/ID sinkron** — verifikasi setelah edit data/string
5. Tidak ada hardcode konten; semua content image pakai `<x-image>` (G51)
6. Path media relatif (`uploads/...` / `media/...`) — jangan simpan full URL di DB

## Format PR

- **Judul:** `feat|fix|docs|refactor(scope): ringkasan singkat`
- **Deskripsi wajib:** apa yang diubah + kenapa, screenshot (jika UI), hasil test
- **Satu PR = satu fokus.** Jangan campur fitur yang tidak berhubungan.
- **PR dari fork:** rebase ke upstream terbaru sebelum buka PR (hindari merge
  commit sampah); pastikan tidak ada perubahan di file core untuk PR project.

## Aturan Teknis (ringkasan — detail di file terkait)

| Aturan | Detail di |
|--------|-----------|
| `resolve_block_asset()` — jangan `asset('storage/')` | `conventions.md`, `theme-development.md` §2A |
| `<x-image>` mandatory untuk content image | `theme-development.md` §2B, Gotcha G51 |
| Form: translation via Form Studio, assignment compliance, jangan asumsikan judul/deskripsi | `conventions.md`, `architecture/form-system.md` |
| SEO breadcrumbs `<x-seo-breadcrumbs>` + heading hierarchy (`h1` pertama, no heading di nav/footer) | `theme-development.md` §3 & §6 |
| Icon: konten/CPT = `lucide:<name>`; admin menu = Material Symbols bare name | `theme-development.md` §2C, `sidebar-menu-system.md` |
| Plugin: `plugin.json` + extend `CmsPluginServiceProvider` + `t()` registry | `plugin-development.md` |
| Static→CMS images via `media:import`, bukan copy ke theme assets | `theme-development.md` §2E, Gotcha G50 |
