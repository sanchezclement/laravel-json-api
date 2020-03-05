<?php
declare(strict_types=1);

namespace JsonApi;

use Illuminate\Support\Facades\Route;

/**
 * Class JsonApi
 * @package JsonApi
 */
class JsonApi
{
    public static function routes()
    {
        Route::prefix('{resource}/{id}/relationships/{relation}')
            ->name('resource.id.relationships.relationship')
            ->group(function () {
                Route::post('', 'JsonApi\Controllers\RelationshipsController@store');
                Route::patch('', 'JsonApi\Controllers\RelationshipsController@patch');
                Route::delete('', 'JsonApi\Controllers\RelationshipsController@delete');
            });
    }
}
