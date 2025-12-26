<?php

namespace App\Services\api\v1\Notification;

use App\Models\NotificationCategory;
use App\Models\NotificationType;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psy\Util\Json;

class NotificationImportService
{
    /**
     * Импортирует категории и типы уведомлений из переданного массива конфига.
     *
     * @param array $categories       Структура из config('notification.categories')
     * @param bool  $updateExisting   true  - upsert (создавать и обновлять),
     *                                false - импортировать только новые записи
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

    public function importCategory(array $categoryData, bool $updateExisting): void
    {
        $category = $this->findOrCreateCategory($categoryData, $updateExisting);

        foreach ($categoryData['types'] as $typeData) {
            $this->importType($category, $typeData, $updateExisting);
        }
    }

    public function importType(NotificationCategory $category, array $data, bool $updateExisting): void
    {
        $typeKey = $data['key'];

        $type = NotificationType::query()
            ->where('key', $typeKey)
            ->where('notification_category_id', $category->id)
            ->first();

        if (!$type) {
            $this->createType($category, $data);

            return;
        }

        if ($updateExisting) {
            $this->updateType($type, $data);
        }
    }

    protected function findOrCreateCategory(array $data, bool $updateExisting): NotificationCategory
    {
        $category = NotificationCategory::firstWhere('key', $data['key']);

        if (!$category) {
            return $this->createCategory($data);
        }

        if ($updateExisting) {
            return $this->updateCategory($category, $data);
        }

        return $category;
    }

    protected function createCategory(array $data): NotificationCategory
    {
        $category = new NotificationCategory();

        $this->fillCategory($category, $data);

        $category->key = $data['key'];

        $category->save();

        return $category;
    }

    protected function createType(NotificationCategory $category, array $data): void
    {
        $type = new NotificationType();

        $type->notification_category_id = $category->id;
        $type->key = $data['key'];

        $this->fillType($type, $data);

        $type->save();
    }

    protected function updateCategory(NotificationCategory $category, array $data): NotificationCategory
    {
        $this->fillCategory($category, $data);

        $category->save();

        return $category;
    }

    protected function updateType(NotificationType $type, array $data): void
    {
        $this->fillType($type, $data);

        $type->save();
    }

    protected function fillCategory(NotificationCategory $category, array $data): void
    {
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
    }

    protected function fillType(NotificationType $type, array $data): void
    {
        $type->name = $data['name'];
        $type->description = $data['description'] ?? null;

        $type->template_title = $data['template']['title'] ?? null;
        $type->template_body = $data['template']['body'] ?? null;

        $type->placeholders = isset($data['placeholders'])
            ? Json::encode($data['placeholders'])
            : null;
    }
}
