# Composer Auto Language Updates

This package will automatically update translations for plugins when you install or update a plugin via composer.

**Coming soon:**

Core support. Theme support.

## How to

#### Add the repo as a composer dependency

`composer require ac-components/composer-plugin-language-update:"dev-develop"`

At the moment developement is taking place on develop. This will probably be updated to be `dev-master` at some point in the future.

#### Define the languages used on your site manually in `/site/conf/arguments/common.yml`

```yaml
languages:
  - 'sv_SE'
  - 'de_DE'
``` 
As this operation cannot rely on having a connection to the database available (for example, if `composer update` is run via http://deploy.synotio.se/), the languages need to be defined manually here.

#### Update your composer.json at `/site/composer.json` to include the following lines

```json
"scripts": {
    "post-package-install": [
        "AngryCreative\\PostUpdateLanguageUpdate::update_t10ns"
    ],
    "post-package-update": [
        "AngryCreative\\PostUpdateLanguageUpdate::update_t10ns"
    ]
}
```

### Tests

cd into the packagage directory and run `composer install` then run `phpunit`. The tests will work best run in our CentOS enviroment.

### Other stuff

This only works if the t10ns are found on the WordPress API, eg:

eg. https://api.wordpress.org/translations/plugins/1.0/?slug=redirection&version=2.7.3

At the moment the translations are _not_ removed on `composer uninstall` but I might implement it if I ever get around to it! / @richard.sweeney
