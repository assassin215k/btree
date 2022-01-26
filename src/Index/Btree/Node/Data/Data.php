<?php

namespace Btree\Index\Btree\Node\Data;

/**
 * Class Data
 *
 * object to store values with same key
 *
 * @package assassin215k/btree
 */
class Data implements DataInterface
{
    private array $values = [];
    private int $total;

    public function __construct(object $value)
    {
        $this->values[] = $value;
        $this->total = 1;
    }

    /**
     * @param object $value
     *
     * @return void
     */
    public function add(object $value): void
    {
        $this->values[] = $value;
        $this->total++;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->values;
    }

    /**
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }
}
