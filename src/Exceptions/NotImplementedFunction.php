<?php
declare(strict_types=1);

namespace App\JsonApi\Exceptions;

use Exception;

/**
 * Class NotImplementedFunction
 * @package App\JsonApi\Exceptions
 */
class NotImplementedFunction extends Exception
{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @since 5.1.0
     */
    public function __construct()
    {
        parent::__construct("This function is not implemented", 500, null);
    }
}
