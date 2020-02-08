<?php
declare(strict_types=1);

namespace JsonApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class BindModels
 * @package JsonApi\Middleware
 */
class BindModels
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $name = $request->route('resource') ?? explode('/', $request->path())[0];

        if (is_null($id = $request->route('id'))) {
            $model = JsonApiBinder::get()->makeModel($name);
        } else {
            $model = JsonApiBinder::get()->findOrFail($name, $id);
        }

        $request->route()->setParameter('model', $model);

        return $next($request);
    }
}
