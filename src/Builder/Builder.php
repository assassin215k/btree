<?php

namespace Btree\Builder;

use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Builder\Exception\EmptyFieldException;
use Btree\Builder\Exception\InvalidConditionValueException;
use Btree\Builder\Exception\MissedFieldValueException;
use Btree\Helper\IndexHelper;
use Btree\Index\Btree\IndexInterface;
use Btree\Index\Btree\Node\Data\DataInterface;
use Btree\Index\Exception\MissedPropertyException;

/**
 * Class Builder
 *
 * Builder to search, filter and order index results
 *
 * @package assassin215k/btree
 */
class Builder implements BuilderInterface
{
    private array $where = [];
    private array $order = [];
    private readonly array $indexes;
    private readonly array $data;

    /**
     * @param IndexInterface[] $indexes
     * @param object[] $data
     */
    public function __construct(array &$indexes, array &$data)
    {
        $this->indexes = $indexes;
        $this->data = $data;
    }

    /**
     * @throws EmptyFieldException
     * @throws InvalidConditionValueException
     * @throws MissedFieldValueException
     *
     * @param string $field
     * @param EnumOperator $operator
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function where(string $field, EnumOperator $operator, mixed $value = null): self
    {
        $this->where = [];

        return $this->andWhere($field, $operator, $value);
    }

    /**
     * @throws EmptyFieldException
     * @throws InvalidConditionValueException
     * @throws MissedFieldValueException
     *
     * @param string $field
     * @param EnumOperator $operator
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function andWhere(string $field, EnumOperator $operator, mixed $value = null): self
    {
        if (empty($field)) {
            throw new EmptyFieldException();
        }
        if ($operator !== EnumOperator::IsNull && is_null($value)) {
            throw new MissedFieldValueException();
        }

        /** @var string $operator */
        switch ($operator) {
            case EnumOperator::IsNull:
                $this->where[$field] = ['op' => $operator];

                return $this;
            case EnumOperator::Between:
                if (count($value) !== 2) {
                    throw new InvalidConditionValueException();
                }

                if (empty($value[0]) || empty($value[1])) {
                    throw new InvalidConditionValueException();
                }

                $value1 = is_int($value[0]) || is_string($value[0]) || is_float($value[0]);
                $value2 = is_int($value[1]) || is_string($value[1]) || is_float($value[1]);
                if ($value1 && $value2) {
                    $this->where[$field] = [
                        'op' => $operator,
                        'val' => max($value[0], $value[1]),
                        'val2' => min($value[0], $value[1])
                    ];

                    return $this;
                }
                throw new InvalidConditionValueException();
            case EnumOperator::Equal:
                if (is_int($value) || is_string($value) || is_float($value) || is_bool($value)) {
                    break;
                }

                throw new InvalidConditionValueException();
            default:
                if (is_int($value) || is_string($value) || is_float($value)) {
                    break;
                }

                throw new InvalidConditionValueException();
        }

        $this->where[$field] = ['op' => $operator, 'val' => $value];

        return $this;
    }

    /**
     * @throws EmptyFieldException
     *
     * @param EnumSort $order
     * @param string $field
     *
     * @return $this
     */
    public function order(string $field, EnumSort $order): self
    {
        $this->order = [];
        $this->addOrder($field, $order);

        return $this;
    }

    /**
     * @throws EmptyFieldException
     *
     * @param EnumSort $order
     * @param string $field
     *
     * @return $this
     */
    public function addOrder(string $field, EnumSort $order): self
    {
        if (empty($field)) {
            throw new EmptyFieldException();
        }

        $this->order[$field] = $order;

        return $this;
    }

    /**
     * @throws MissedPropertyException
     *
     * @return array
     */
    public function run(): array
    {
        if (!count($this->where)) {
            $data = $this->data;
            if (count($this->order)) {
                $this->sortData($data);
            }

            return $data;
        }

        /** @var IndexInterface $index */
        list($index, $fields) = $this->selectIndex();

        if (is_null($index)) {
            $data = $this->filter($this->data, $this->where);
            $this->sortData($data);

            return $data;
        }

        $lastOperator = null;
        $key = IndexHelper::DATA_PREFIX;
        $key2 = null;
        foreach ($fields as $field) {
            $operator = $this->where[$field]['op'];

            if ($operator !== EnumOperator::IsNull && $operator !== EnumOperator::Equal) {
                $lastOperator = $operator;
            }

            if ($operator === EnumOperator::Between) {
                $key2 = $key . $this->where[$field]['val2'];
            }
            $key .= $operator === EnumOperator::IsNull ? IndexHelper::NULL : $this->where[$field]['val'];

            unset($this->where[$field]);
        }

        if (!$lastOperator) {
            $data = $this->filter($index->search($key), $this->where);
            $this->sortData($data);

            return $data;
        }

        switch ($lastOperator) {
            case EnumOperator::LessThen:
                $data = $index->lessThan($key);
                break;
            case EnumOperator::LessThenOrEqual:
                $data = $index->lessThanOrEqual($key);
                break;
            case EnumOperator::GreaterThen:
                $data = $index->greaterThan($key);
                break;
            case EnumOperator::GreaterThenOrEqual:
                $data = $index->greaterThanOrEqual($key);
                break;
            case EnumOperator::Between:
                $data = $index->between($key, $key2);
                break;
        }

        $data = $this->filter($data, $this->where);
        $this->sortData($data);

        return $data;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    private function sortData(array &$data): void
    {
        usort($data, function (object $a, object $b) {
            foreach ($this->order as $field => $order) {
                if ($a->$field === $b->$field) {
                    continue;
                }

                if ($order === EnumSort::ASC) {
                    return ($a->$field < $b->$field) ? -1 : 1;
                }

                return ($a->$field > $b->$field) ? -1 : 1;
            }

            return 0;
        });
    }

    /**
     * @return array
     */
    private function selectIndex(): array
    {
        $indexMaxLength = 0;
        $indexKey = null;
        $selectedFields = [];

        foreach ($this->indexes as $key => $index) {
            $indexLength = 0;
            $fields = [];
            foreach ($index->getFields() as $field) {
                if (!array_key_exists($field, $this->where)) {
                    break;
                }

                $indexLength++;

                $fields[] = $field;

                $operator = $this->where[$field]['op'];
                if ($operator === EnumOperator::Equal || $operator === EnumOperator::IsNull) {
                    continue;
                }

                break;
            }

            if ($indexLength > $indexMaxLength) {
                $indexMaxLength = $indexLength;
                $indexKey = $key;
                $selectedFields = $fields;
            }
        }

        return [
            $indexMaxLength ? $this->indexes[$indexKey] : null,
            $selectedFields
        ];
    }

    /**
     * @throws MissedPropertyException
     *
     * @param array $whereArray
     * @param array $data
     *
     * @return array
     */
    private function filter(array $data, array $whereArray): array
    {
        return array_filter($data, function (object $item) use ($whereArray): bool {
            foreach ($whereArray as $field => $where) {
                $this->checkField($item, $field);

                if ($where['op'] === EnumOperator::IsNull) {
                    if (is_null($item->$field)) {
                        continue;
                    }

                    return false;
                }

                switch ($where['op']) {
                    case EnumOperator::Equal:
                        if ($item->$field !== $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::GreaterThen:
                        if ($item->$field <= $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::GreaterThenOrEqual:
                        if ($item->$field < $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::LessThen:
                        if ($item->$field >= $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::LessThenOrEqual:
                        if ($item->$field > $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::Between:
                        if ($item->$field > $where['val'] && $item->$field > $where['val2']) {
                            return false;
                        }

                        if ($item->$field < $where['val'] && $item->$field < $where['val2']) {
                            return false;
                        }
                        break;
                }
            }

            return true;
        });
    }

    /**
     * @throws MissedPropertyException
     *
     * @param string $field
     * @param object $item
     *
     * @return void
     */
    private function checkField(object $item, string $field): void
    {
        if (!property_exists($item, $field)) {
            throw new MissedPropertyException($field, $item);
        }
    }
}
