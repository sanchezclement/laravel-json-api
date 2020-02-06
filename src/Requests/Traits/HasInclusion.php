<?php
declare(strict_types=1);

namespace App\JsonApi\Requests\Traits;

use App\JsonApi\Requests\Params\Inclusion;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasInclusion
 * @package App\JsonApi\Request\Traits
 */
trait HasInclusion
{
    /**
     * @var Inclusion
     */
    private $inclusions;

    public function __construct()
    {
        $this->addRules([
            'include' => 'string'
        ]);

        $this->afterValidation(function () {
            $this->initializeInclusion();
        });
    }

    private final function initializeInclusion()
    {
        $this->inclusions = Inclusion::makeFromString($this->get('include', ''));
    }

    /**
     * @return Inclusion
     */
    public final function getInclusions(): Inclusion
    {
        return $this->inclusions;
    }

    /**
     * @param Builder $builder
     */
    public final function preprocess(Builder $builder)
    {
        $builder->with($this->all());
    }
}
