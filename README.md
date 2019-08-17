# eZ Clean Up Versions Script
This script will remove old versions of all contents for eZ Platform.

## versions
* Branch v1 & Tags 1.x : eZ Platform v1.x
* Branch v2 & Tags 2.x : eZ Platform v2.x
* Branch v3 & Tags 3.x : eZ Platform v3.x (in beta)

## Install Package
```bash
composer require matthewkp/ez-clean-up-versions
```
## Register Bundle
No manual registration is required

## Arguments
* Add -v to have the full information regarding content and version ids removals.
* Add --keep to set the number of versions to keep
* Add --locationId to set the root location id to start processing

## Add in crontab
```php
// /etc/cron.d/<your_cron_file> or /var/spool/cron/apache
0 0 * * * <user> cd <your_site_path> && php bin/console matthewkp:ez-clean-up-versions --env=<ENV> > 2>&1
```
