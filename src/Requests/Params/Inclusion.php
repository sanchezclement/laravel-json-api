<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Class Inclusion
 * @package App\JsonApi\Requests\Params
 */
class Inclusion extends Collection
{
    /**
     * Create a new resource instance.
     *
     * @param string $include
     * @return Inclusion
     */
    public static function makeFromString(string $include)
    {
        return new static(strlen($include) ? explode(',', $include) : []);
    }

    /**
     * @param $collection
     */
    public final function loadMissing($collection)
    {
        if ($collection instanceof Model) {
            $collection = ModelCollection::make([$collection]);
        }

        $collection->loadMissing($this->mapWithKeys(function (string $relationName) {
            return [$relationName => function (Relation $builder) {
            }];
        }));
    }
}
