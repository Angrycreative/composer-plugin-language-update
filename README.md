# Composer Auto Language Updates

This package will automatically update translations for WordPress core, themes & plugins when you install or update them via composer.*

*\* This only works if the translations are available via the WordPress API.*

## Installation instructions

#### 1 Add this repository as a composer dependency

First, add this .git repo to the `repositories` array in `site\composer.json` 

```json
{
  "type": "git",
  "url": "https://github.com/Angrycreative/composer-plugin-language-update.git"
}
```

#### 2 Run: `composer require ac-components/composer-plugin-language-update:"*"`

##### Define the languages used on your site and the path to your wp-content directory via the extras object at `/site/composer.json`

```json
"extra": {
  "wordpress-languages": [ "sv_SE", "en_GB", "da_DK" ],
  "wordpress-path-to-content-dir": "public/wp-content"
 }
``` 

We need to add a list of locales manually (ie, not check the database) as this operation cannot rely on having a connection to the database available.

#### 3 Update the scripts object in your `/site/composer.json` to include the following lines

```json
"scripts": {
  "post-package-install": [
    "AngryCreative\\PostUpdateLanguageUpdate::install_t10ns"
  ],
  "post-package-update": [
    "AngryCreative\\PostUpdateLanguageUpdate::update_t10ns"
  ]
}
```

That's it. Next time you run a `composer update|install` the t10ns for the relevant packages will be installed automatically.

### Tests

If you're testing, this package must be installed as a part of WordPress installation. You should ideally remove the entire `wp-content/languages` directory, so as to make sure the package behaves as expected.

Obviously you should probably do this on seperate branch, so you don't remove t10ns accidentaly when you run the tests.

`cd` into the packagage directory and run `composer test`.

You **may** need to run the tests as root to avoid permissions errors when creating the directories.

### WT(Actual)F

#### I can has missing translation plz?

This only works if the t10ns are found on the WordPress API, eg. https://api.wordpress.org/translations/plugins/1.0/?slug=redirection&version=2.7.3

#### I can haz clean up after you?

At the moment the translations are _not_ removed on `composer uninstall`. Pull requests welcome!
