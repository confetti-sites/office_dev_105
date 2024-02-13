Color
===============

---------------
Decorations
---------------
This is a list of all available decorations for this component. Decorations are used to add extra functionality to a component.

| Type    | Comment                                           |
|---------|---------------------------------------------------|
| default | If no value is given, this will be used           |
| label   | Label is used as a field title in the admin panel |

This make it possible do the following: `$var->color('profile_color')->label('Profile color')->default('#000000')`

---------------
Admin input field
---------------
This is the code for the admin input field. This is the field where the user can enter the content.

```php
<label class="block mt-4 text-sm">
    <span class="">{{ $component->getDecoration('label')['value'] }}</span>
    <input type="color"
           x-bind="field"
           name="{{ $contentId }}"
           value="{{ $contentStore->find($component->key) ?? $component->getDecoration('default')['value'] }}"
    >
</label>
```

---------------
Getters
---------------
This is the code that will be used to render the value on the website.

```php
<?php

declare(strict_types=1);

namespace Confetti\Components;

use Confetti\Helpers\ComponentStandard;

return new class extends ComponentStandard {
    public function get(): string
    {
        return $this->contentStore->findOneData($this->id) ?? '#000000';
    }
};
```

