<?php
declare(strict_types=1);

namespace JsonApi\Requests\Params;

use Illuminate\Support\Str;

/**
 * Class FilterParser
 * @package App\JsonApi\Requests\Params
 */
class SortingParser
{
    /**
     * @var array
     */
    private $param;

    /**
     * FilterParser constructor.
     */
    public function __construct()
    {
        $this->param = Sorting::make();
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }

    /**
     * @param string $param
     * @return Sorting
     */
    public function parse($param): Sorting
    {
        if ($param) {
            foreach (explode(',', $param) as $field) {
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = 'desc';
                } else {
                    $direction = 'asc';
                }

                $field = Str::snake($field);

                $this->param->add($field, $direction);
            }
        }

        return $this->param;
    }
}
