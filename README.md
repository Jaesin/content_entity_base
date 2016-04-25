[![Branch 8.x-1.x](https://travis-ci.org/Jaesin/content_entity_base.svg?branch=8.x-1.x)](https://travis-ci.org/Jaesin/content_entity_base)

Content Entity Base
===================

This module's sole purpose is to help simplify creating custom bundlable, translatable entities.

We have tried to move as much code as possible into the content entity base module so only the entity definition is required for yoru custom entity module.

You can get started with this module by copying the tests/modules/ceb_test module and start renaming with search/replace or use [drupal console](https://github.com/hechoendrupal/DrupalConsole) to genereate a custom entity.

This is currently a pre-release version but has been moderately tested with Drupal 8.0.3. 

### Dependencies ###

1. Entity API: https://www.drupal.org/project/entity

### Drupal console ###

Console support is provided in a secondary module (**console_ceb**).

Usage: `console generate:entity:ceb --module foo_module --entity-class FooEntity --entity-name foo_content --label "Foo Content"`

**Notes:*** The console chain command does work with the `generate:entity:ceb` command because the necessary templates are not found.
