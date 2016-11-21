# eZ Clean Up Versions Script
This script will remove old versions of all contents for eZ Platform.

Currently tested on eZ Platform v1.6.
## Install Package
```bash
composer require matthewkp/EzCleanUpVersions "~0.1"
```
## Register Bundle
```php
// ezpublish/EzPublishKernel.php

class EzPublishKernel extends Kernel
{
    ...
    public function registerBundles()
    {
        ...
        $bundles = array(
            ...
            new Matthewkp\EzCleanUpVersionsBundle()
            ...
        );
        ...
    }
}
```
## Add in crontab
```php
// /etc/cron.d/<your_cron_file>
0 0 * * * <user> cd <your_site_path> && php app/console matthewkp:ez-clear-up-versions --env=<ENV> > 2>&1
```

## Configure
Edit Resources/config/settings.yml as you wish
