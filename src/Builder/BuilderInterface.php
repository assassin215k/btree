<?php

namespace Btree\Builder;

use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Builder\Exception\EmptyFieldException;
use Btree\Builder\Exception\InvalidConditionValueException;

/**
 * Class BuilderInterface
 *
 * @package assassin215k/btree
 */
interface BuilderInterface
{
    public function __construct(array &$indexes, array &$data);

    /**
     * @throws EmptyFieldException
     * @throws InvalidConditionValueException
     *
     * @param mixed $value
     *
     * @param string $field
     * @param EnumOperator $operator
     *
     * @return $this
     */
    public function where(string $field, EnumOperator $operator, mixed $value): self;

    /**
     * @throws EmptyFieldException
     * @throws InvalidConditionValueException
     *
     * @param mixed $value
     *
     * @param string $field
     * @param EnumOperator $operator
     *
     * @return $this
     */
    public function andWhere(string $field, EnumOperator $operator, mixed $value): self;

    /**
     * @throws EmptyFieldException
     *
     * @param EnumSort $order
     * @param string $field
     *
     * @return $this
     */
    public function order(string $field, EnumSort $order): self;

    /**
     * @throws EmptyFieldException
     *
     * @param EnumSort $order
     * @param string $field
     *
     * @return $this
     */
    public function addOrder(string $field, EnumSort $order): self;

    public function run(): array;
}
