<?php
declare(strict_types=1);

namespace App\JsonApi\Requests\Params;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class FilterParser
 * @package App\JsonApi\Requests\Params
 */
class Filter
{
    const OPERATORS = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*', 'is null', 'is not null'
    ];

    /**
     * @var array
     */
    private $param;

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $operators = ['='];

    /**
     * @var string
     */
    private $boolean = 'and';

    /**
     * FilterParser constructor.
     * @param array $param
     */
    public function __construct(array $param)
    {
        $this->param = $param;
    }

    /**
     * @param array $param
     * @return static
     */
    public static function make(array $param)
    {
        return new static($param);
    }

    /**
     * @param Builder $builder
     */
    public function process(Builder $builder)
    {
        $this->parseArray($builder, $this->param);
    }

    /**
     * @param Builder $builder
     * @param array $array
     */
    public function parseArray(Builder $builder, array $array): void
    {
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $builder->where(function (Builder $builder) use ($value) {
                    $this->parseParam($builder, $value);
                });
            } else if ($this->isOperator($key)) {
                $this->parseParamWithOperator($key, $builder, $value);
            } else if ($this->isBoolean($key)) {
                $this->parseParamWithBoolean($key, $builder, $value);
            } else if ($this->hasChildField($value)) {
                $this->parseParamWithRelation($key, $builder, $value);
            } else {
                $this->parseParamWithField($key, $builder, $value);
            }
        }
    }

    /**
     * @param Builder $builder
     * @param string $relation
     * @param array $value
     */
    private function parseParamWithRelation(string $relation, Builder $builder, array $value)
    {
        [$relation, $operator] = $this->extractRelationOperator($relation);

        $builder->has($relation, $operator, 1, $this->getBoolean(), function (Builder $builder) use ($value) {
            $this->parseParam($builder, $value);
        });
    }

    /**
     * @param string $field
     * @return array
     */
    private function extractRelationOperator(string $field): array
    {
        return strpos($field, '!') === 0 ? [substr($field, 1), '<'] : [$field, '>='];
    }

    /**
     * @param string $operator
     * @param Builder $builder
     * @param $value
     */
    private function parseParamWithOperator(string $operator, Builder $builder, $value)
    {
        $this->operators[] = $operator;

        $this->parseParam($builder, $value);

        array_pop($this->operators);
    }

    /**
     * @param string $boolean
     * @param Builder $builder
     * @param $value
     */
    private function parseParamWithBoolean(string $boolean, Builder $builder, $value)
    {
        $this->boolean = $boolean;

        $this->parseParam($builder, $value);

        $this->boolean = 'and';
    }

    /**
     * @param string $field
     * @param Builder $builder
     * @param $value
     */
    private function parseParamWithField(string $field, Builder $builder, $value)
    {
        $this->field = $field;

        $this->parseParam($builder, $value);

        $this->field = null;
    }

    /**
     * @param Builder $builder
     * @param $value
     */
    private function parseParam(Builder $builder, $value): void
    {
        if (is_array($value)) {
            $this->parseArray($builder, $value);
        } else {
            $this->applyFilter($builder, $value);
        }
    }

    /**
     * @param Builder $builder
     * @param string $value
     */
    private function applyFilter(Builder $builder, $value)
    {
        $model = $builder->getModel();
        [$this->field, $operator] = $this->extractRelationOperator($this->field);

        if (method_exists($model, $this->field) && $model->{$this->field}() instanceof Relation) {
            $this->applyRelationFilter($builder, $value, $operator);
        } else {
            $builder->where($this->field, $this->getOperator(), $value, $this->getBoolean());
        }
    }

    /**
     * @param Builder $builder
     * @param string|null $value
     * @param string $operator
     */
    private function applyRelationFilter(Builder $builder, ?string $value, string $operator)
    {
        \Log::info("relation");
        \Log::info($value);


        $callback = $value ? function (Builder $builder) use ($value) {
            \Log::info("hello");
            $builder->whereKey(explode(',', $value));
        } : null;

        $builder->has($this->field, $operator, 1, $this->getBoolean(), $callback);

    }

    /**
     * @param array $array
     * @return bool
     */
    private function hasChildField($array): bool
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if ($this->isField($key) || $this->hasChildField($value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function getOperator(): string
    {
        return end($this->operators);
    }

    /**
     * @return string
     */
    private function getBoolean(): string
    {
        $boolean = $this->boolean;

        $this->boolean = 'and';

        return $boolean;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isOperator(string $string): bool
    {
        return in_array(strtolower($string), static::OPERATORS);
    }

    /**
     * @param string $string
     * @return bool
     */
    private function isBoolean(string $string): bool
    {
        return $string === 'or' || $string === 'and';
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isField(string $key)
    {
        return !$this->isBoolean($key) && !$this->isOperator($key);
    }
}
