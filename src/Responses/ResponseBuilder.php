<?php
declare(strict_types=1);

namespace JsonApi\Responses;

/**
 * Class ResponseBuilder
 * @package JsonApi\Responses
 */
class ResponseBuilder
{
    /**
     * @return AcceptedResponse
     */
    public function accepted()
    {
        return new AcceptedResponse;
    }

    /**
     * @return DeletedResponse
     */
    public function deleted()
    {
        return new DeletedResponse;
    }

    /**
     * @return ErrorResponse
     */
    public function error()
    {
        return new ErrorResponse;
    }

    /**
     * @return NoContentResponse
     */
    public function noContent()
    {
        return new NoContentResponse;
    }

    /**
     * @return UnprocessableEntityResponse
     */
    public function unprocessableEntity()
    {
        return new UnprocessableEntityResponse;
    }
}
