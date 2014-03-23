EditCustomOptions
=================

This Magento module makes custom options editable by customer in order overview.

All options can be changed as long as they don't affect the final price.

Changes will be tracked in order status history.

By default, only orders that are *new* or *holded* can be changed. This is defined in `config.xml`:

    <default>
        <sales>
            <editcustomoptions>
                <allowed_order_state>new,holded</allowed_order_state>
            </editcustomoptions>
        </sales>
    </default>

# Installation

1. Install with modman or copy `app` into the Magento installation directory
2. Flush Magento cache

# Uninstallation

1. Just remove the files. The module does not make changes to the database.

# Known issues
- If the product does not exist anymore, the options cannot be changed
- If the custom option configuration of the product have been changed in between, the behavior is undefined