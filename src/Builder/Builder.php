<?php

namespace Btree\Builder;

use Btree\Builder\Enum\EnumOperator;
use Btree\Builder\Enum\EnumSort;
use Btree\Builder\Exception\EmptyFieldException;
use Btree\Builder\Exception\InvalidConditionValueException;
use Btree\Helper\IndexHelper;
use Btree\Index\Btree\IndexInterface;
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

    public function __construct(private readonly array &$indexes, private readonly array &$data)
    {
    }

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
    public function where(string $field, EnumOperator $operator, mixed $value): self
    {
        $this->where = [];

        return $this->andWhere($field, $operator, $value);
    }

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
    public function andWhere(string $field, EnumOperator $operator, mixed $value): self
    {
        if (empty($field)) {
            throw new EmptyFieldException();
        }

        /** @var string $operator */
        switch ($operator) {
            case EnumOperator::IsNull:
                $this->where[$field] = ['op' => $operator];

                return $this;
            case EnumOperator::Beetwen:
                if (count($value) !== 2) {
                    throw new InvalidConditionValueException();
                }

                if (empty($value[0]) || empty($value[1])) {
                    throw new InvalidConditionValueException();
                }

                $value1 = is_int($value[0]) || is_string($value[0]) || is_float($value[0]);
                $value2 = is_int($value[1]) || is_string($value[1]) || is_float($value[1]);
                if ($value1 && $value2) {
                    break;
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
     * @return array
     */
    public function run(): array
    {
        if (!count($this->where)) {
            if (count($this->order)) {
                $this->sortData($this->data);
            }

            return $this->data;
        }

        $fields = array_keys($this->where);
        $index = $this->selectIndex($fields);

        if (is_null($index)) {
            $data = $this->filter($this->data, $this->where);
            $this->sortData($data);

            return $data;
        }

        // work with index here

        return [];
    }

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
                        if ($item->$field === $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::GreateThen:
                        if ($item->$field <= $where['val']) {
                            return false;
                        }
                        break;

                    case EnumOperator::GreateThenOrEqual:
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

                    case EnumOperator::Beetwen:
                        if ($item->$field > $where['val'][0] && $item->$field > $where['val'][1]) {
                            return false;
                        }

                        if ($item->$field < $where['val'][0] && $item->$field < $where['val'][1]) {
                            return false;
                        }
                }
            }

            return true;
        });
    }

    private function checkField(object $item, string $field): void
    {
        if (empty($item->$field)) {
            throw new MissedPropertyException($field, $item);
        }
    }

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

    private function selectIndex(array &$fields): ?IndexInterface
    {
        if (!count($fields)) {
            return null;
        }

        $indexKey = IndexHelper::getIndexName($fields);
        if (array_key_exists($indexKey, $this->indexes)) {
            return $this->indexes[$indexKey];
        }

        array_pop($fields);

        return $this->selectIndex($fields);
    }
}
