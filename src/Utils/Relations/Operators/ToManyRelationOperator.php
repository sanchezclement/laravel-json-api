<?php
declare(strict_types=1);

namespace JsonApi\Utils\Relations\Operators;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class ToManyRelationOperator
 * @package App\JsonApi\Utils\Relations\Operators
 */
class ToManyRelationOperator implements IRelationOperator
{
    /**
     * @var string
     */
    private $policy;

    /**
     * @var bool
     */
    private $process;

    /**
     * ToManyRelationOperator constructor.
     * @param string $policy
     * @param bool $process
     */
    public function __construct(string $policy, bool $process = true)
    {
        $this->policy = $policy;
        $this->process = $process;
    }

    /**
     * @param string $policy
     * @param bool $process
     * @return static
     */
    public static function make(string $policy, bool $process = true)
    {
        return new static($policy, $process);
    }

    /**
     * @param array $data
     */
    public function validate(array $data): void
    {
        Validator::make($data, [
            '*' => 'array',
            '*.id' => 'required_with:*',
            '*.type' => 'required_with:*'
        ])->validate();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function resolve(array $data)
    {
        return collect($data)->mapToGroups(function (array $item) {
            return [$item['type'] => $item['id']];
        })->each(function ($ids, $type) {
            $collection = JsonApiBinder::get()->findModel($type, $ids);

            if ($collection->count() !== count($ids)) {
                abort(422);
            }

            return $collection;
        })->collapse();
    }

    /**
     * @param Gate $gate
     * @param $data
     * @return bool
     */
    public function authorize(Gate $gate, $data): bool
    {
        foreach ($data as $datum) {
            if (!$gate->raw($this->policy, $datum)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Model $model
     * @param string $name
     * @param $related
     * @return void
     */
    public function apply(Model $model, string $name, $related): void
    {
        if ($this->process) {
            $model->{$name}()->sync($related);
        }
    }
}
