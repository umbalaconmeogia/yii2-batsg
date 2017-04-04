# yii2-batsg
[![Build Status](https://travis-ci.org/khoawasabi/yii2-batsg.svg?branch=master)](https://travis-ci.org/khoawasabi/yii2-batsg)
Khoa is applying PHPUnit to test it out.

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
