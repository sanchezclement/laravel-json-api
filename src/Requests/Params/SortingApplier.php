<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class SortingApplier
 * @package App\JsonApi\Requests\Params
 */
class SortingApplier
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * FilterApplier constructor.
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return static
     */
    public static function make(string $field, string $direction)
    {
        return new static($field, $direction);
    }

    /**
     * @param Builder $builder
     */
    public function applyTo(Builder $builder)
    {
        $builder->orderBy($this->field, $this->direction);
    }
}
