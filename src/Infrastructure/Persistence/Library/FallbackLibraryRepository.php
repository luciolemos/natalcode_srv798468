<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Library;

use App\Domain\Library\LibraryRepository;

class FallbackLibraryRepository implements LibraryRepository
{
    public function findPublishedBooks(): array
    {
        return [];
    }

    public function findAllBooksForAdmin(): array
    {
        return [];
    }

    public function findBookByIdForAdmin(int $id): ?array
    {
        return null;
    }

    public function createBook(array $data): int
    {
        return 0;
    }

    public function updateBook(int $id, array $data): bool
    {
        return false;
    }

    public function deleteBook(int $id): bool
    {
        return false;
    }

    public function findActiveCategories(): array
    {
        return [];
    }

    public function findAllCategoriesForAdmin(): array
    {
        return [];
    }

    public function findCategoryByIdForAdmin(int $id): ?array
    {
        return null;
    }

    public function createCategory(array $data): int
    {
        return 0;
    }

    public function updateCategory(int $id, array $data): bool
    {
        return false;
    }

    public function setCategoryActive(int $id, bool $isActive): bool
    {
        return false;
    }
}
