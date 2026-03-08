# hubspot-time-slot-booking

Uses HubSpot API to display available time slots.

## Repository Structure

```
index.html              # Static event landing page (standalone)
server.js               # Local dev server (proxies HubSpot API on :8080)
event-landing-pages/    # WordPress plugin
```

## WordPress Plugin — Event Landing Pages

A generic, white-label WordPress plugin for creating event landing pages with HubSpot time slot picker or form embed integration.

### Requirements

- WordPress 6.0+
- PHP 7.4+
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/) (ACF PRO 6.x+) — required for the global settings page and all custom fields
- A [HubSpot](https://www.hubspot.com/) account with one of the following configured:
  - **Meetings tool** — for the time slot picker booking method. Create a meeting link in HubSpot (Settings > Meetings) and note the slug (e.g. `username/meeting-name`).
  - **Forms** — for the form embed booking method. You'll need your Portal ID and the Form ID from HubSpot.

### Setup

1. Install and activate ACF PRO.
2. Install and activate the Event Landing Pages plugin.
3. Go to **Events > Settings** in wp-admin:
   - **HubSpot tab** — Enter your Portal ID (found in HubSpot under Settings > Account Management). Optionally add a Private App Access Token for server-side API calls.
   - **Default Brand tab** — Set your brand name, logo, and website. These are used as defaults for all events (or the WordPress site logo is used as a fallback).
   - **Typography tab** — Optionally set Google Font families for headings and body text.
4. Create a new Event under **Events > Add New** and configure the booking method, event details, and URL.

### Releasing a New Version

The plugin uses [plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) to deliver updates through the WordPress admin. Because the plugin lives in a subdirectory of this repo, releases use **attached zip assets** (not the auto-generated source archive).

**Steps to release:**

1. Bump the version in `event-landing-pages/event-landing-pages.php` (both the `Version:` header and the `ELP_VERSION` constant).

2. Commit and push to `main`.

3. Create a plugin-only zip (from the repo root):

   ```bash
   cd event-landing-pages
   zip -r ../event-landing-pages-v1.2.0.zip . -x "*.git*"
   cd ..
   ```

4. Create a GitHub release with a matching tag:

   ```bash
   gh release create v1.2.0 event-landing-pages-v1.2.0.zip \
     --title "v1.2.0" \
     --notes "Description of changes"
   ```

5. WordPress sites running the plugin will see the update in **Dashboard > Updates** (checked periodically by WP cron).

**Important:** The release tag (e.g. `v1.2.0`) must match the `Version:` in the plugin header, or the update checker won't detect a newer version.
