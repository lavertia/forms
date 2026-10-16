# Lavertia Forms

**Experimental v0.2 Composer package - no stable release yet.**

Owns server-side PHP descriptions of `Form`, `TextInput`, `Select`, `Textarea`, and `Checkbox`, independent of Panels. Requires [`lavertia/support`](https://github.com/lavertia/support) Component Protocol v1.

```php
use Lavertia\Forms\Components\{Form, TextInput, Select, Checkbox};

$form = Form::make('profile')
    ->action('/profile')
    ->method('patch')
    ->components([
        TextInput::make('name')->required(),
        Select::make('role')->options(['editor' => 'Editor']),
        Checkbox::make('notifications'),
    ]);
```

There are **no automatic database writes**. Laravel must own authentication, CSRF, authorization, validation, and Eloquent persistence at every endpoint. A Form without `action()` is a non-submitting preview.

The UI runtimes currently live in [`lavertia/panels` PR #6](https://github.com/lavertia/panels/pull/6); this package carries no React/Vue/Svelte dependency. Moving existing `Lavertia\Panels\Components\*` PHP API aliases to this package is a follow-on integration step, not yet shipped. All code repositories remain private until the maintainer chooses to publish them.

Run `composer test` and `composer lint` after installing a compatible Support package. Cross-repository CI requires a read-only `LAVERTIA_READ_TOKEN` while Support is private.

[Release tracking](https://github.com/lavertia/lavertia/issues/3)
