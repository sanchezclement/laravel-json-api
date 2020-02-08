<?php
declare(strict_types=1);

namespace JsonApi\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JsonApiModel
 * @package App\JsonApi\Models
 */
class JsonApiModel extends Model
{
    /**
     * @param string $name
     * @param array $parameters
     * @return Model
     */
    public static function makeFromName(string $name, ...$parameters): Model
    {
        return static::getClassFromName($name)::make(...$parameters);
    }

    /**
     * @param string $name
     * @return string|Model
     */
    public static function getClassFromName(string $name): string
    {
        return config('resources.nameToModel')[$name];
    }

    /**
     * @param string $name
     * @param $id
     * @return Model
     */
    public static function findFromNameOrFail(string $name, $id): Model
    {
        return static::getClassFromName($name)::findOrFail($id);
    }

    /**
     * @param string $name
     * @param $id
     * @return Model
     */
    public static function findFromName(string $name, $id): ?Model
    {
        return static::getClassFromName($name)::find($id);
    }

    /**
     * @param Model $model
     * @return Model
     */
    public static function getName(Model $model): string
    {
        return config('resources.modelToName')[get_class($model)];
    }

    /**
     * @param Model $model
     * @return array
     */
    public static function toIdentifier(Model $model): array
    {
        return ['type' => static::getName($model), 'id' => $model->getKey()];
    }
}
