<?php

namespace App\Services\api\v1\Notification;

use App\Exceptions\v1\RepositoryException;
use App\Models\NotificationCategory;
use App\Repositories\v1\Notification\NotificationCategoryRepository;
use App\Repositories\v1\Notification\NotificationTypeRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationImportService
{
    public function __construct(
        protected NotificationCategoryRepository $categoryRepository,
        protected NotificationTypeRepository $typeRepository,
    )
    {}

    /**
     * Imports categories and types obtained from the passed config array
     *
     * @param array $categories Structure from config('notification.categories')
     * @param bool  $updateExisting true - upsert,
     *                              false - imports only new records.
     */
    public function importFromConfig(array $categories, bool $updateExisting = true): bool
    {
        DB::beginTransaction();

        try {
            foreach ($categories as $categoryData) {
                $this->importCategory($categoryData, $updateExisting);
            }
        } catch (Exception $e) {
            Log::error('Failed to import notification categories', ['exception' => $e]);

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }

    /**
     * @throws RepositoryException
     */
    public function importCategory(array $categoryData, bool $updateExisting): void
    {
        $category = $this->findOrCreateCategory($categoryData, $updateExisting);

        foreach ($categoryData['types'] as $typeData) {
            $this->importType($category, $typeData, $updateExisting);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function importType(NotificationCategory $category, array $data, bool $updateExisting): void
    {
        $type = $this->typeRepository->findByKeyInCategory($category, $data['key']);

        if (!$type) {
            $this->typeRepository->create($category, $data);

            return;
        }

        if ($updateExisting) {
            $this->typeRepository->update($type, $data);
        }
    }

    /**
     * @throws RepositoryException
     */
    protected function findOrCreateCategory(array $data, bool $updateExisting): NotificationCategory
    {
        $category = $this->categoryRepository->findByKey($data['key']);

        if (!$category) {
            return $this->categoryRepository->create($data);
        }

        if ($updateExisting) {
            return $this->categoryRepository->update($category, $data);
        }

        return $category;
    }
}
