<?php

namespace App\Exceptions;

/**
 * 业务上的异常抛出, 保证其它 Exception 有 log 可寻
 *
 */
class CustomException extends \Exception
{
}
