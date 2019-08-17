# eZ Clean Up Versions Script
This script will remove old versions of all contents for eZ Platform.

## versions
Branch v1 & Tags 1.x : eZ Platform v1.x

Branch v2 & Tags 2.x : eZ Platform v2.x (Currently testing)

## Install Package
```bash
composer require matthewkp/ez-clean-up-versions
```
## Register Bundle
```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    ...
    public function registerBundles()
    {
        ...
        $bundles = [
            ...
            new Matthewkp\EzCleanUpVersionsBundle\MatthewkpEzCleanUpVersionsBundle(),
            ...
        ];
        ...
    }
}
```
## Add in crontab
```php
// /etc/cron.d/<your_cron_file> or /var/spool/cron/apache
0 0 * * * <user> cd <your_site_path> && php app/console matthewkp:ez-clean-up-versions --env=<ENV> > 2>&1
```

## Arguments
Add -v to have the full information regarding content and version ids removals.
Add --keep to set the number of versions to keep
Add --locationId to set the root location id to start processing
