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

#[Route('/api/vegetables')]
final class VegetableController
{
    public function __construct(private CollectionService $collectionService) {}

    #[Route(methods: [Request::METHOD_GET])]
    public function getVegetables(#[MapQueryString] ProduceFiltersInput $filtersInput): JsonResponse
    {
        return new JsonResponse(['data' => $this->collectionService->getCollection(ProduceType::Vegetable, $filtersInput->toArray())]);
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function addVegetables(#[MapRequestPayload(validationFailedStatusCode: JsonResponse::HTTP_BAD_REQUEST)] ProduceInput $produceInput): JsonResponse
    {
        $this->collectionService->addToCollection($produceInput);

        return new JsonResponse(['data' => $this->collectionService->getCollection(ProduceType::Vegetable)], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteVegetable(int $id): JsonResponse
    {
        $this->collectionService->removeFromCollection(ProduceType::Vegetable, $id);

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
