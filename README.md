[![Branch 8.x-1.x](https://travis-ci.org/Jaesin/content_entity_base.svg?branch=8.x-1.x)](https://travis-ci.org/Jaesin/content_entity_base)

Content Entity Base
===================

This module's sole purpose is to help simplify creating custom bundlable, translatable entities.

We have tried to move as much code as possible into the content entity base module so only the entity definition is required for yoru custom entity module.

You can get started with this module by copying the tests/modules/ceb_test module and start renaming with search/replace. Note: There is not that much in the module though.

@todo Add Drupal console support to this module.

This is currently a pre-release version but has been moderately tested with Drupal 8.0.3.


### Known issues

In Drupal 8.0.x all content entity permissions will show up under "Content Entity
Base" because you were not allowed to set the provider of permissions until 8.1.x.
( See: <http://drupal.org/node/2673726> )
