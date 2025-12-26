<?php

namespace App\Repositories\v1\Notification;

use App\Exceptions\v1\RepositoryException;
use App\Models\NotificationCategory;
use App\Repositories\Repository;

class NotificationCategoryRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(NotificationCategory::class);
    }

    public function findByKey(string $key): ?NotificationCategory
    {
        return NotificationCategory::firstWhere('key', $key);
    }

    /**
     * @throws RepositoryException
     */
    public function create(array $data): NotificationCategory
    {
        $category = new NotificationCategory();

        $this->fill($category, $data);

        $category->key = $data['key'];

        $this->save($category);

        return $category;
    }

    /**
     * @throws RepositoryException
     */
    public function update(NotificationCategory $category, array $data): NotificationCategory
    {
        $this->fill($category, $data);

        $this->save($category);

        return $category;
    }

    protected function fill(NotificationCategory $category, array $data): void
    {
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
    }
}
