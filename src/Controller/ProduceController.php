<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CollectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\ProduceType;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Dto\ProduceInput;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\ProduceFiltersInput;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/produces')]
class ProduceController
{
    public function __construct(private CollectionService $collectionService) {}

    #[Route(methods: [Request::METHOD_POST])]
    public function addProduces(#[MapRequestPayload(type: ProduceInput::class, validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] array $producesInputs): JsonResponse
    {
        foreach ($producesInputs as $produceInput) {
            $this->collectionService->addToCollection($produceInput);
        }

        return new JsonResponse(['data' => $this->collectionService->getCollection()], JsonResponse::HTTP_CREATED);
    }

    #[Route(methods: [Request::METHOD_GET])]
    public function getProduces(#[MapQueryString] ProduceFiltersInput $filtersInput): JsonResponse
    {
        $produceType = ProduceType::tryFrom($filtersInput->type ?? '');

        return new JsonResponse(['data' => $this->collectionService->getCollection($produceType, $filtersInput->toArray())]);
    }
}