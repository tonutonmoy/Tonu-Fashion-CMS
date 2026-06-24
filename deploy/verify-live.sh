#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"
echo "GIT_HEAD=$(git rev-parse --short HEAD)"
echo "GIT_LOG=$(git log -1 --oneline)"
grep -q 'data-turbo="false"' resources/views/layouts/admin.blade.php && echo "ADMIN_TURBO_OFF=1" || echo "ADMIN_TURBO_OFF=0"
grep -q 'Profit & Loss' resources/views/partials/admin/sidebar.blade.php && echo "REPORTS_FLAT=1" || echo "REPORTS_FLAT=0"
grep -q 'adminBuilderMode' app/Providers/AppServiceProvider.php && echo "BUILDER_MODE_COMPOSER=1" || echo "BUILDER_MODE_COMPOSER=0"
ls -la public/build/assets/admin-entry-*.js 2>/dev/null | tail -1
VERIFY_LIVE_OK
