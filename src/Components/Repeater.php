<?php

declare(strict_types=1);

namespace Lavertia\Forms\Components;

use InvalidArgumentException;
use Lavertia\Support\Components\AbstractComponent;
use Lavertia\Support\Components\SupportsComponents;
use Lavertia\Support\Contracts\ComposesComponents;
use Lavertia\Support\Contracts\FieldComponent;

final class Repeater extends AbstractComponent implements ComposesComponents, FieldComponent
{
    use SupportsComponents;

    private const MAX_ROWS = 500;

    private function __construct(private readonly string $name) {}

    public static function make(string $name): self
    {
        if (preg_match('/^[A-Za-z][A-Za-z0-9_.-]{0,127}$/D', $name) !== 1) {
            throw new InvalidArgumentException('Invalid repeater field name.');
        }

        return (new self($name))->key($name);
    }

    public function getType(): string
    {
        return 'repeater';
    }

    public function getFieldName(): string
    {
        return $this->name;
    }

    public function label(string $label): static
    {
        return $this->withProp('label', $label);
    }

    /**
     * @param list<\Lavertia\Support\Contracts\Component> $components
     */
    public function schema(array $components): static
    {
        return $this->components($components);
    }

    public function minItems(int $count): static
    {
        if ($count < 0 || $count > self::MAX_ROWS) {
            throw new InvalidArgumentException('Repeater minimum must be between 0 and 500.');
        }

        return $this->withProp('minItems', $count);
    }

    public function maxItems(int $count): static
    {
        if ($count < 1 || $count > self::MAX_ROWS) {
            throw new InvalidArgumentException('Repeater maximum must be between 1 and 500.');
        }

        return $this->withProp('maxItems', $count);
    }

    public function defaultItems(int $count): static
    {
        if ($count < 0 || $count > self::MAX_ROWS) {
            throw new InvalidArgumentException('Repeater initial count must be between 0 and 500.');
        }

        return $this->withProp('defaultItems', $count);
    }

    /**
     * Prefill each row with JSON-safe field values. These are client display
     * defaults, never a trusted source for authorization or persistence.
     *
     * @param list<array<string, mixed>> $rows
     */
    public function defaultRows(array $rows): static
    {
        if (! array_is_list($rows) || count($rows) > self::MAX_ROWS) {
            throw new InvalidArgumentException('Repeater defaults must be a list of at most 500 rows.');
        }

        foreach ($rows as $row) {
            if (! is_array($row) || ($row !== [] && array_is_list($row))) {
                throw new InvalidArgumentException('Repeater default rows must be associative arrays.');
            }

            foreach (array_keys($row) as $key) {
                if (! is_string($key) || preg_match('/^[A-Za-z][A-Za-z0-9_.-]{0,127}$/D', $key) !== 1) {
                    throw new InvalidArgumentException('Invalid repeater default field name.');
                }
            }
        }

        return $this->withProp('defaultRows', $rows);
    }

    public function addable(bool $enabled = true): static
    {
        return $this->withProp('addable', $enabled);
    }

    public function deletable(bool $enabled = true): static
    {
        return $this->withProp('deletable', $enabled);
    }

    public function reorderable(bool $enabled = true): static
    {
        return $this->withProp('reorderable', $enabled);
    }

    public function collapsible(bool $enabled = true): static
    {
        return $this->withProp('collapsible', $enabled);
    }

    public function clientSide(): static
    {
        return $this->withProp('clientSide', true);
    }

    /** @return array<string, mixed> */
    public function getProps(): array
    {
        return [
            'name' => $this->name,
            'minItems' => 0,
            'maxItems' => 100,
            'defaultItems' => 1,
            'addable' => true,
            'deletable' => true,
            'reorderable' => true,
            'collapsible' => false,
            'clientSide' => true,
            ...parent::getProps(),
        ];
    }
}
