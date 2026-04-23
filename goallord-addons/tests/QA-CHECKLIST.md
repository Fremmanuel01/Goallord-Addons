# Goallord Addons — QA Checklist

Two-part verification: automated (smoke test) and manual (browser).

## 1. Automated smoke test

Requires PHP 7.4+ on PATH. From the plugin root:

```
php tests/smoke-test.php
```

Expected: `N checks, 0 failures` and exit code 0.

The smoke test stubs WordPress and Elementor, loads every plugin PHP file,
and introspects classes to confirm the widget is wired correctly. It does
not render HTML — that is the manual phase below.

## 2. Install into WordPress

1. Copy or upload `goallord-addons.zip` via **Plugins -> Add New -> Upload**.
2. Activate.
3. Confirm no fatal errors appear on any admin screen.
4. Confirm no "requires Elementor" admin notice when Elementor is active.

## 3. Elementor editor — widget panel

- [ ] Open any page with Elementor.
- [ ] In the widget panel, confirm a **Goallord Addons** category is visible.
- [ ] The **Announcement & News** widget appears under that category.
- [ ] Its icon is rendered (posts-grid).

## 4. Drag into canvas — default state

- [ ] Drag the widget into a section.
- [ ] Three sample items render immediately with placeholder content.
- [ ] The section header ("Announcements & News") renders above the grid.
- [ ] No layout breaks; cards are equal height by default.

## 5. Content controls

- [ ] **Layout** dropdown — cycle through all 5 layouts. Each renders without
      empty cells or overflow:
    - [ ] Editorial Grid
    - [ ] Minimal List
    - [ ] Featured + Grid
    - [ ] Sidebar Bulletin
    - [ ] Timeline
- [ ] **Columns** — set to 1 / 2 / 3 / 4. Grid re-flows.
- [ ] **Image Ratio** — every preset renders a correctly-proportioned image frame.
- [ ] **Section Header** — toggle Show off, then back on.
- [ ] **Repeater** — add a 4th item, reorder items, delete an item, duplicate
      an item; canvas updates on each change.
- [ ] **Item fields** — for one item:
    - [ ] Upload a different image
    - [ ] Pick an icon (Font Awesome)
    - [ ] Fill in Extra Meta ("5 min read")
    - [ ] Fill in Full Content (WYSIWYG) with a paragraph and a bullet list
    - [ ] Enable Badge, change Badge Style to "Urgent"
    - [ ] Change CTA text
    - [ ] Paste a URL into Link
- [ ] **Display Options** — toggle each Show X switch; matching element appears/disappears.
- [ ] **Excerpt length** — set to 12; descriptions truncate to 12 words.
- [ ] **Title HTML tag** — change h3 -> h2; DOM reflects it.
- [ ] **CTA Arrow Character** — change from arrow to a different character.

## 6. Layout settings

- [ ] **Grid Gap** — slider changes spacing between cards.
- [ ] **Card Text Alignment** — left / center / right reflects in cards.
- [ ] **Card Heights** — switch between Equal and Natural, visible difference
      when card contents are unequal length.

## 7. Animation controls

- [ ] **Enable Entrance Animations** off — cards are visible immediately with
      no transform/opacity transition.
- [ ] Toggle on, preview the page. On scroll, cards fade-up with stagger.
- [ ] Change **Animation Style** (fade / fade-up / fade-down / zoom / slide-left /
      slide-right); reload preview, each variant animates correctly.
- [ ] **Duration / Delay / Stagger Delay** sliders — adjust and reload;
      animation timing visibly changes.
- [ ] **Enable Card Hover Lift** off — cards no longer lift on hover.
- [ ] **Enable Image Zoom on Hover** off — images no longer zoom.
- [ ] With hover on, change **Image Zoom Scale** — image zooms to the new value
      on hover.
- [ ] OS setting "Reduce Motion" on (macOS: System Settings -> Accessibility,
      Windows: Settings -> Ease of Access -> Display) — reload frontend page;
      cards appear immediately with no transforms.

## 8. Style controls (Style tab)

For each section (Wrapper, Section Header, Card, Image, Icon, Meta, Badge,
Card Title, Description, Read More / CTA, Divider):

- [ ] Change at least one color — change reflects live in canvas.
- [ ] Change typography (font-size) — change reflects live.
- [ ] Change padding/margin (where applicable) — change reflects live.
- [ ] Hover tab where present — hover state visible when mousing over card.

## 9. Responsive

Use Elementor's responsive mode (bottom-left of editor):

- [ ] Tablet — grid collapses (default 3 -> 2 columns for editorial/featured).
- [ ] Mobile — grid collapses to 1 column.
- [ ] Header alignment responsive control — different value per breakpoint.
- [ ] Grid Gap responsive control — different value per breakpoint.

## 10. Frontend (published page, logged out)

- [ ] View the published page in a private/incognito window.
- [ ] No console errors.
- [ ] Widget CSS is loaded (search DevTools Network for `announcement-news.css`).
- [ ] Widget JS is loaded (search for `announcement-news.js`).
- [ ] IntersectionObserver reveal works on scroll.
- [ ] Images lazy-load (check Network tab — images below fold load only after scrolling).
- [ ] Links open in the configured target (_blank / _self).
- [ ] Keyboard — Tab through the card links; focus styles visible.

## 11. Uninstall / Deactivate

- [ ] Deactivate the plugin — admin loads without errors.
- [ ] Pages using the widget fall back to Elementor's "widget missing" placeholder.
- [ ] Re-activate — widgets render again from saved data.

## 12. Edge cases

- [ ] Add an item with **no image** and confirm the featured layout's hero
      collapses to a single column instead of showing an empty cell.
- [ ] Clear the **Link** field on an item; no broken `<a href>` is rendered.
- [ ] Set **Excerpt length** to 0; full description renders.
- [ ] Enable Show Full Content globally and set Full Content on two items;
      both render their long-form content (not only the featured hero).
- [ ] Remove the Section Header title + subtitle + eyebrow; header block
      disappears entirely (no empty wrapper).
- [ ] Toggle **Show Divider** off; cards in Minimal/Sidebar/Timeline layouts
      have no bottom border.
