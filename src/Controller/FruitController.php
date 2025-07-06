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

#[Route('/api/fruits')]
final class FruitController
{
    public function __construct(private CollectionService $collectionService) {}

    #[Route(methods: [Request::METHOD_GET])]
    public function getFruits(#[MapQueryString] ProduceFiltersInput $filtersInput): JsonResponse
    {
        return new JsonResponse(['data' => $this->collectionService->getCollection(ProduceType::Fruit, $filtersInput->toArray())]);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function addFruits(#[MapRequestPayload(validationFailedStatusCode: JsonResponse::HTTP_BAD_REQUEST)] ProduceInput $produceInput): JsonResponse
    {
        $this->collectionService->addToCollection($produceInput);

        return new JsonResponse(['data' => $this->collectionService->getCollection(ProduceType::Fruit)], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteFruit(int $id): JsonResponse
    {
        $this->collectionService->removeFromCollection(ProduceType::Fruit, $id);

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
