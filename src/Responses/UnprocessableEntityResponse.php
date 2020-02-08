<?php
declare(strict_types=1);

namespace JsonApi\Responses;

/**
 * Class UnprocessableEntityResponse
 * @package JsonApi\Responses
 */
class UnprocessableEntityResponse extends ErrorResponse
{
    /**
     * AcceptedResponse constructor.
     */
    public function __construct()
    {
        parent::__construct(422);
    }

    /**
     * @return static
     */
    public static function make(): self
    {
        return new static();
    }
}
