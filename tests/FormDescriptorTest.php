<?php

declare(strict_types=1);

namespace Lavertia\Forms\Tests;

use InvalidArgumentException;
use Lavertia\Forms\Components\Checkbox;
use Lavertia\Forms\Components\Form;
use Lavertia\Forms\Components\Select;
use Lavertia\Forms\Components\Textarea;
use Lavertia\Forms\Components\TextInput;
use PHPUnit\Framework\TestCase;

final class FormDescriptorTest extends TestCase
{
    public function test_json_protocol_contract_and_field_types(): void
    {
        $node = Form::make('profile')
            ->action('/account/profile')
            ->method('patch')
            ->label('Profile')
            ->submitLabel('Save')
            ->components([
                TextInput::make('display_name')->required()->value('Sara'),
                Select::make('role')->options(['owner' => 'Owner', 'editor' => 'Editor'])->value('editor'),
                Textarea::make('bio')->rows(4)->value('Hello'),
                Checkbox::make('notify')->checked(),
            ])
            ->toArray();

        self::assertSame('form', $node['type']);
        self::assertSame('root', $node['key']);
        self::assertSame('/account/profile', $node['props']->action);
        self::assertSame('patch', $node['props']->method);
        self::assertSame('Save', $node['props']->submitLabel);
        self::assertSame(
            ['text-input', 'select', 'textarea', 'checkbox'],
            array_column($node['components'], 'type'),
        );
        self::assertSame('root/display_name', $node['components'][0]['key']);
        self::assertSame('Sara', $node['components'][0]['props']->value);
        self::assertJson(json_encode($node, JSON_THROW_ON_ERROR));
    }

    public function test_missing_action_does_not_allow_submission(): void
    {
        $props = Form::make('preview')->toArray()['props'];

        self::assertSame('post', $props->method);
        self::assertFalse(property_exists($props, 'action'));
    }

    public function test_only_local_post_put_patch_actions_are_permitted(): void
    {
        $rejected = 0;

        foreach (['get', 'delete', 'trace'] as $method) {
            try {
                Form::make('profile')->method($method);
                self::fail('Expected a method validation exception.');
            } catch (InvalidArgumentException) {
                $rejected++;
            }
        }

        foreach (['https://malicious.example', '//malicious.example/collect', '/form#fragment', '/\\evil'] as $url) {
            try {
                Form::make('profile')->action($url);
                self::fail('Expected an action URL exception.');
            } catch (InvalidArgumentException) {
                $rejected++;
            }
        }

        self::assertSame(7, $rejected);
    }

    public function test_form_can_opt_in_to_targeted_inertia_refresh(): void
    {
        $node = Form::make('profile')
            ->refreshOnSuccess(['users', 'statistics', 'users'])
            ->toArray();

        self::assertSame(['users', 'statistics'], $node['props']->refreshOnSuccess);
    }

    public function test_refresh_props_must_use_safe_inertia_prop_names(): void
    {
        $rejected = 0;

        foreach ([[], ['__proto__'], ['foo.bar'], [''], ['users', 12]] as $invalid) {
            try {
                Form::make('profile')->refreshOnSuccess($invalid);
                self::fail('Expected invalid refresh props to fail.');
            } catch (InvalidArgumentException) {
                $rejected++;
            }
        }

        self::assertSame(5, $rejected);
    }

    public function test_numeric_select_options_survive_php_serialization(): void
    {
        $values = Select::make('level')->options([0 => 'None', 1 => 'Beginner'])->toArray();

        self::assertSame(['None', 'Beginner'], $values['props']->options);
    }

    public function test_field_name_and_size_bounds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TextInput::make('bad field');
    }
}
