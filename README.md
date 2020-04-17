# yii2-batsg
My style, my best practice and my libraries when using yii2 framework.

## Overview

* Convention when designing database.
* gii modification
 
## Convention when designing database

* All DB table should contain following columns.

Column name | Data type | Description
---|---|---
id | serial | primary key
data_status | int | 1: new, 2: updated, 9: deleted
created_by | int | Create user id. Set automatically.
created_at | int | Created timestamp. Set automatically.
updated_by | int | Update user id. Set automatically.
updated_at | int | Updated timestamp. Set automatically.


## Install yii2-batsg into your project.
```php
composer require umbalaconmeogia/yii2-batsg
```

### Explanation about composer `require`, `update` and `install`

|command|change composer.json|change composer.lock|
|--|--|--|
|require|Y|Y|
|update|N|Y|
|install|N|N|

* require: `composer require umbalaconmeogia/yii2-batsg`
  This will update or install the newest version. `composer.json` and `composer.lock` will be updated as well.
* update: `composer update umbalaconmeogia/yii2-batsg`
  This will update the package with the highest version respects to your `composer.json`. The entry in `composer.lock` will be updated.
* install: `composer install umbalaconmeogia/yii2-batsg`
  This will install version specified in the lock file
