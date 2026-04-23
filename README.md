# Goallord Addons

Premium Elementor widget suite. Each widget is built for editorial-grade design, deep customization, and performance.

## Widgets

| Widget | Purpose |
|---|---|
| **Goallord News & Announcements** | 5-layout news/announcement grid with dynamic WP_Query support |
| **Goallord Daily Schedule** | Grouped daily rhythm / timetable with 5 layouts |

More widgets are planned — see the plugin's `widgets/` folder for the current set.

## Repository layout

```
.
├── goallord-addons/          # The plugin — this is what ships
│   ├── goallord-addons.php   # Main plugin file (plugin header lives here)
│   ├── includes/             # Plugin core (bootstrap, widget manager, helpers)
│   ├── widgets/              # Each widget as a self-contained class
│   ├── assets/               # CSS + JS per widget
│   ├── tests/                # Smoke test + manual QA checklist
│   └── readme.txt            # WordPress.org-style readme
├── build-zip.ps1             # Build script — produces goallord-addons.zip
├── goallord-addons.zip       # Build artifact (gitignored)
└── README.md                 # This file
```

## Development

1. Clone the repo.
2. Symlink `goallord-addons/` into your WordPress `wp-content/plugins/` directory (or copy).
3. Activate via WordPress admin → Plugins.
4. Edit any page with Elementor → widgets appear under the **Goallord Addons** category.

### Build the installable zip

From the repo root (PowerShell, Windows):

```powershell
.\build-zip.ps1
```

Produces `goallord-addons.zip` — forward-slash zip paths, excludes `tests/` and stray image files, ready to upload via WordPress admin **Plugins → Add New → Upload Plugin**.

### Run the smoke test

Standalone PHP test (stubs WP + Elementor, loads all plugin files, introspects classes):

```bash
php goallord-addons/tests/smoke-test.php
```

Expected: `N checks, 0 failures` + exit code 0.

See `goallord-addons/tests/QA-CHECKLIST.md` for the full manual browser QA checklist.

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Elementor 3.5.0+ (free version is enough)

## Adding a new widget

1. Create `goallord-addons/widgets/class-your-widget.php` (namespace `Goallord\Addons\Widgets`, extends `Widget_Base`).
2. Register assets in `goallord-addons/includes/class-goallord-addons.php` (`register_styles()` / `register_scripts()`).
3. Add one line to `$widgets_map` in `goallord-addons/includes/class-widgets-manager.php`.
4. Follow the naming convention: widget title is `Goallord <Purpose>`, CSS namespace is `.goallord-<abbr>__`, asset handle is `goallord-<slug>`.

## License

GPLv2 or later.
