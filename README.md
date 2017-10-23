# Composer Auto Language Updates

This package will automatically update translations for WordPress core, themes & plugins when you install or update them via composer.*

*\* This only works if the t10ns are available via the WordPress API.*

## Installation instructions

#### Add this repo as a composer dependency

Add this .git repo to the `repositories` array in `site\composer.json` 

```json
{
    "type": "git",
    "url": "https://git.synotio.se/ac-components/composer-plugin-language-update.git"
}
```
Then run: `composer require ac-components/composer-plugin-language-update:"*"`

*At the moment developement is taking place on branch develop.*

#### Define the languages used on your site via the extras object at `/site/composer.json`

```json
"extra": {
  "wordpress-languages": [ "sv_SE", "en_UK", "da_DK" ], 
 }
``` 

(We need to do this manually as this operation cannot rely on having a connection to the database available. The occurs, for example, when `composer update` is run via http://deploy.synotio.se/.)

#### Update the scripts object in your `/site/composer.json` to include the following lines

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

If you're testing, you should probably remove the entire `wp-content/languages` directory. This will make sure the relevant directories are created when running the scripts.

Obviously you should probably do this on another branch other than master, so you don't remove t10ns accidentaly when you run the tests.

`cd` into the packagage directory and run `composer test`.

### WTF

#### I can not has translation plz?

This only works if the t10ns are found on the WordPress API, eg. https://api.wordpress.org/translations/plugins/1.0/?slug=redirection&version=2.7.3

#### I can haz clean up your shit?

At the moment the translations are _not_ removed on `composer uninstall` but I might implement it if I ever get around to it / @richard.sweeney

#### Can i haz not add the list of languages manually?

For our automated builds, composer will have no access to the database, so this has to be configured manually. Such is the price we pay for glory.
