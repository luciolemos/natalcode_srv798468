<?php

declare(strict_types=1);

namespace App\Domain\Library;

interface LibraryRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function findPublishedBooks(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllBooksForAdmin(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function findBookByIdForAdmin(int $id): ?array;

    /**
     * @param array<string, mixed> $data
     */
    public function createBook(array $data): int;

    /**
     * @param array<string, mixed> $data
     */
    public function updateBook(int $id, array $data): bool;

    public function deleteBook(int $id): bool;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findActiveCategories(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllCategoriesForAdmin(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function findCategoryByIdForAdmin(int $id): ?array;

    /**
     * @param array<string, mixed> $data
     */
    public function createCategory(array $data): int;

    /**
     * @param array<string, mixed> $data
     */
    public function updateCategory(int $id, array $data): bool;

    public function setCategoryActive(int $id, bool $isActive): bool;
}
