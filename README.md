# gossamerCMS2.0
complete rewrite from the ground up of the gossamer CMS framework

All separate repositories (json/html and database) are now 1 unified repository, specified in /app/config/config/yml.
Future: add an API setting

The EventListener has been lightened up, and the system only uses the EventListener for raised events - natural flow is now handled through Filters.

It also gave me a chance to implement all the "I should have done it this way" changes from working with various releases of version 1.