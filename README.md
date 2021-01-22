# Wikimedia Commons DAM Connector

:warning: Important read: [Reusing content outside Wikimedia: How to comply with a file's license requirements](https://commons.wikimedia.org/wiki/Commons:Reusing_content_outside_Wikimedia#How_to_comply_with_a_file's_license_requirements)

Requires `ezsystems/ezplatform-connector-dam` — available only on commercial distributions (Ibexa DXP Content, Ibexa DXP Experience, Ibexa DXP Commerce or former eZ Platform Enterprise Edition)

## Install

1. `composer require adriendupuis/ezplatform-connector-commons;`
1. Add `AdrienDupuis\EzPlatformConnectorCommonsBundle\AdrienDupuisEzPlatformConnectorCommonsBundle::class => ['all' => true],` to bundles.php
1. In the configuration, add the DAM name `'commons'` to `content.dam` array — see [DAM Configuration](https://doc.ibexa.co/en/latest/guide/config_connector/#dam-configuration) for details.

## TODO

- Enhance credit line – see https://commons.wikimedia.org/wiki/Commons:Credit_line
