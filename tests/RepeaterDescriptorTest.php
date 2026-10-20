<?php

declare(strict_types=1);

namespace Lavertia\Forms\Tests;

use InvalidArgumentException;
use Lavertia\Forms\Components\Repeater;
use Lavertia\Forms\Components\TextInput;
use PHPUnit\Framework\TestCase;

final class RepeaterDescriptorTest extends TestCase
{
    public function test_repeater_serializes_nested_schema_and_row_defaults(): void
    {
        $node = Repeater::make('items')
            ->label('Invoice items')
            ->schema([
                TextInput::make('description')->required(),
                TextInput::make('quantity')->value('1'),
            ])
            ->minItems(1)
            ->maxItems(40)
            ->defaultRows([
                ['description' => 'First', 'quantity' => 2],
                ['description' => 'Second', 'quantity' => 4],
            ])
            ->addable()
            ->deletable()
            ->reorderable()
            ->collapsible()
            ->clientSide()
            ->toArray();

        self::assertSame('repeater', $node['type']);
        self::assertSame('items', $node['props']->name);
        self::assertSame(40, $node['props']->maxItems);
        self::assertSame(1, $node['props']->minItems);
        self::assertTrue($node['props']->clientSide);
        self::assertSame('First', $node['props']->defaultRows[0]['description']);
        self::assertSame(['text-input', 'text-input'], array_column($node['components'], 'type'));
        self::assertSame('root/description', $node['components'][0]['key']);
        self::assertJson(json_encode($node, JSON_THROW_ON_ERROR));
    }

    public function test_invalid_repeater_configuration_is_rejected(): void
    {
        foreach ([
            fn () => Repeater::make('items with spaces'),
            fn () => Repeater::make('items')->maxItems(0),
            fn () => Repeater::make('items')->maxItems(501),
            fn () => Repeater::make('items')->minItems(-1),
            fn () => Repeater::make('items')->defaultItems(501),
            fn () => Repeater::make('items')->defaultRows([['__proto__' => 'unsafe']]),
            fn () => Repeater::make('items')->defaultRows([['0' => 'not associative']]),
        ] as $invalid) {
            try {
                $invalid();
                self::fail('Expected invalid repeater configuration to fail.');
            } catch (InvalidArgumentException) {
                // Expected.
            }
        }
    }

    public function test_nested_repeaters_have_stable_protocol_keys(): void
    {
        $tree = Repeater::make('orders')
            ->schema([
                Repeater::make('lines')
                    ->schema([TextInput::make('sku')]),
            ])->toArray();

        self::assertSame('root/lines', $tree['components'][0]['key']);
        self::assertSame('root/lines/sku', $tree['components'][0]['components'][0]['key']);
    }
}
