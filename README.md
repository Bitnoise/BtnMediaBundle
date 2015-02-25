BtnMediaBundle
==============

[Icons source](https://github.com/teambox/Free-file-icons/)

### Step 1: Add MediaBundle in your composer.json (private repo)

```js
{
    "require": {
        "bitnoise/media-bundle": "dev-master",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:Bitnoise/BtnMediaBundle.git"
        }
    ],
}
```

### Step 2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Btn\MediaBundle\BtnMediaBundle(),
        new Liip\ImagineBundle\LiipImagineBundle(),
        new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
    );
}
```

### Step 3: Import MediaBundle routing

``` yaml
# app/config/routing.yml
btn_media:
    resource: "@BtnMediaBundle/Controller/"
    type:     annotation
    prefix:   /
    
_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
```

### Step 4: Update your database schema

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 5: Add BtnMediaBundle to the assetic.bundle config

``` yml
# app/config/config.yml
assetic:
    #...
    bundles:
        #...
        - BtnMediaBundle
```

### Step 7: add Gaufrette Configuration
``` yml
# app/config/config.yml
# Gaufrette Configuration
knp_gaufrette:
    adapters:
        btn_media:
            local:
                directory: uploads/media
                create: true
    filesystems:
        btn_media:
            adapter: btn_media
    stream_wrapper: ~

```
