# Lavertia Forms

**Experimental v0.2 Composer package - no stable release yet.**

Owns server-side PHP descriptions of `Form`, `TextInput`, `Select`, `Textarea`, `Checkbox`, and `Repeater`, independent of Panels. Requires [`lavertia/support`](https://github.com/lavertia/support) Component Protocol v1.

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

### Local-first repeaters and targeted refresh (experimental)

Describe repeated fields in PHP. The matching React/Vue/Svelte panel adapters
add, delete and reorder rows locally without requests; the complete nested
payload is sent to Laravel on submit.

```php
use Lavertia\Forms\Components\{Form, Repeater, TextInput};

Form::make('invoice')
    ->action('/invoices')
    ->method('post')
    ->refreshOnSuccess(['lavertia']) // opt-in Inertia prop reload, not browser refresh
    ->components([
        Repeater::make('items')
            ->label('Invoice items')
            ->schema([
                TextInput::make('description')->required(),
                TextInput::make('quantity')->value('1')->required(),
            ])
            ->minItems(1)
            ->maxItems(50)
            ->defaultRows([['description' => 'Pencil', 'quantity' => 2]])
            ->reorderable()
            ->collapsible()
            ->clientSide(),
    ]);
```

A submission includes items as a list of associative arrays. Validate
items with Laravel Form Requests (including list/max/required rules), authorize
every mutation and perform relational writes in a transaction. Never trust row
IDs, prices, or ownership data just because they came from the UI.

refreshOnSuccess() accepts top-level Inertia prop names. Today Panels exposes
the whole component tree as the lavertia prop; granular data-only props are a
follow-on task. Modal actions are not yet part of this API.

There are **no automatic database writes**. Laravel must own authentication, CSRF, authorization, validation, and Eloquent persistence at every endpoint. A Form without `action()` is a non-submitting preview.

The UI runtimes currently live in [`lavertia/panels` PR #6](https://github.com/lavertia/panels/pull/6); this package carries no React/Vue/Svelte dependency. Moving existing `Lavertia\Panels\Components\*` PHP API aliases to this package is a follow-on integration step, not yet shipped. The source repositories are public; this package remains an experimental preview without a stable release.

Run `composer test` and `composer lint` after installing a compatible Support package. Public cross-repository GitHub Actions run real PHP tests without a private-read token.

[Release tracking](https://github.com/lavertia/lavertia/issues/3)

## Security and license

MIT licensed for Lavertia-owned code. See [LICENSE.md](LICENSE.md) and [SECURITY.md](SECURITY.md). Never open a public issue containing credentials or personal records.
