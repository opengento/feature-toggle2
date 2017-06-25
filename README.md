# Feature Toggle module for Magento 2

_by Opengento_

![Logo Opengento Feature Toggle for Magento 2](https://opengento.github.io/feature-toggle2/logo.png)

This module allows you to use the Feature Flags, or sometimes called "Feature Toggle" functionality.

To use a toggle, you just have to call the helper, like this:

```php
/* @var $toggleHelper \Opengento\FeatureToggle2\Helper\Toggle */
if ($toggleHelper->isToggleActive('my-feature-flag')) {
    // Toggle activated
} else {
    // Toogle inactive
}
```

## XML sample

The filename is `toggles.xml`, and you have to put it in the `etc/` directory of your module.

```xml
<?xml version="1.0"?>
<toggles xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Opengento_FeatureToggle2:etc/toggles.xsd">
    <toggle id="my-feature-flag">
        <label>My Feature Flag</label>
        <description>This feature flag is a sample toggle.</description>
    </toggle>
</toggles>
```

## Contributing

See [CONTRIBUTING.md](https://github.com/opengento/feature-toggle2/blob/master/CONTRIBUTING.md).

## Maintainers

See [Contributors list](https://github.com/opengento/feature-toggle2/graphs/contributors).

