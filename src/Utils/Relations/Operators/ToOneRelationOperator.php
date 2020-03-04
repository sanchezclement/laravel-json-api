<?php
declare(strict_types=1);

namespace JsonApi\Utils\Relations\Operators;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class ToOneRelationOperator
 * @package App\JsonApi\Utils\Relations\Operators
 */
class ToOneRelationOperator implements IRelationOperator
{
    /**
     * @var string
     */
    private $policy;

    /**
     * @var callable
     */
    private $process;

    /**
     * ToOneRelationOperator constructor.
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
            'id' => 'required',
            'type' => 'required'
        ])->validate();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function resolve(array $data)
    {
        return JsonApiBinder::get()->find($data['type'], $data['id']) ?? abort(422);
    }

    /**
     * @param Gate $gate
     * @param mixed $data
     * @return bool
     */
    public function authorize(Gate $gate, $data): bool
    {
        return $gate->raw($this->policy, $data) ? true : false;
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
            $model->{$name}()->associate($related);
        }
    }
}
