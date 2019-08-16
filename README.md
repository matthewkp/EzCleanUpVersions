# eZ Clean Up Versions Script
This script will remove old versions of all contents for eZ Platform.

Works on eZ Platform version > 1.6 as it uses the new PermissionResolver service introduced in ezpublish-kernel v6.6.

## versions
Branch v1 & Tags 0.1.x : eZ Platform v1.x
Branch v2 & Tags 0.2.x : eZ Platform v2.x

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
        $bundles = array(
            ...
            new Matthewkp\EzCleanUpVersionsBundle\MatthewkpEzCleanUpVersionsBundle(),
            ...
        );
        ...
    }
}
```
## Add in crontab
```php
// /etc/cron.d/<your_cron_file>
0 0 * * * <user> cd <your_site_path> && php app/console matthewkp:ez-clean-up-versions --env=<ENV> > 2>&1
```

## Configure
Edit Resources/config/default_settings.yml as you wish
