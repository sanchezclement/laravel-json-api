<?php
declare(strict_types=1);

namespace JsonApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use JsonApi\Binders\JsonApiBinder;

/**
 * Class JsonApiMiddleware
 * @package JsonApi\Middleware
 */
class JsonApiMiddleware
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

        if (JsonApiBinder::get()->isResolvable($name)) {
            if (is_null($id = $request->route('id'))) {
                $model = JsonApiBinder::get()->makeModel($name);
            } else {
                $model = JsonApiBinder::get()->findOrFail($name, $id);
            }

            $request->route()->setParameter('id', $model);
            $request->route()->setParameter('model', $model);
        }

        return $next($request);
    }
}
