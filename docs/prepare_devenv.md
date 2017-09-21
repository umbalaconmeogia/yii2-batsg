## Directory structure

yii2-batsg.devenv
|
|--yii2-batsg      /* this library */
|
|--yii2            /* yii2 framework */
|
|--.buildpath
|
|--.project

## Change in yii2 composer.json

```json
    "require": {
	    ....
		"umbalaconmeogia/yii2-batsg": "*"
    },
	"repositories": [
	    {
		    "type": "vcs",
			"url": "../yii2-batsg"
		}
	]
```