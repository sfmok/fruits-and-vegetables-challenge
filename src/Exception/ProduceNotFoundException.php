<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpFoundation\Response;

#[WithHttpStatus(statusCode: Response::HTTP_NOT_FOUND)]
final class ProduceNotFoundException extends Exception
{
    public function __construct(int $id)
    {
        parent::__construct(sprintf('Produce with ID %d not found.', $id));
    }
} 