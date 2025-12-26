<?php

namespace App\Repositories\v1\Notification;

use App\Exceptions\v1\RepositoryException;
use App\Models\NotificationCategory;
use App\Models\NotificationType;
use App\Repositories\Repository;

class NotificationTypeRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(NotificationType::class);
    }

    public function findByKeyInCategory(NotificationCategory $category, string $key): ?NotificationType
    {
        return NotificationType::query()
            ->where('key', $key)
            ->where('notification_category_id', $category->id)
            ->first();
    }

    /**
     * @throws RepositoryException
     */
    public function create(NotificationCategory $category, array $data): NotificationType
    {
        $type = new NotificationType();

        $this->fill($type, $data);

        $type->notification_category_id = $category->id;
        $type->key = $data['key'];

        $this->save($type);

        return $type;
    }

    /**
     * @throws RepositoryException
     */
    public function update(NotificationType $type, array $data): NotificationType
    {
        $this->fill($type, $data);

        $this->save($type);

        return $type;
    }

    protected function fill(NotificationType $type, array $data): void
    {
        $type->name = $data['name'];
        $type->description = $data['description'] ?? null;

        $type->template_title = $data['template']['title'] ?? null;
        $type->template_body = $data['template']['body'] ?? null;

        $type->placeholders = $data['placeholders'] ?? null;
    }
}
