# Composer Auto Language Updates

This package will automatically update translations for plugins when you install or update a plugin via composer.

You _must_ define the languages used on your site manually in `/site/conf/arguments/common.yml` like this:

```yaml
languages:
  - 'sv_SE'
  - 'de_DE'
``` 
You _must_ also update your composer.json at `/site/composer.json` to include the following lines:

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

cd into the packagage directory and run `composer install` then run `phpunit`.

### Other stuff

This only works if the t10ns are found on the WordPress API, eg:

eg. https://api.wordpress.org/translations/plugins/1.0/?slug=redirection&version=2.7.3
