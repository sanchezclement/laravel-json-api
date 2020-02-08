<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Sorting
 * @package App\JsonApi\Requests\Params
 */
class Sorting
{
    /**
     * @var array
     */
    private $sortings = [];

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @param string $field
     * @param string $direction
     * @return void
     */
    public function add(string $field, string $direction): void
    {
        $this->sortings[] = [$field, $direction];
    }

    /**
     * @param Builder $builder
     */
    public function process(Builder $builder)
    {
        if (count($this->sortings)) {
            foreach ($this->sortings as [$field, $direction]) {
                SortingApplier::make($field, $direction)->applyTo($builder);
            }
        } else {
            $builder->orderBy($builder->getModel()->getQualifiedKeyName());
        }
    }
}
