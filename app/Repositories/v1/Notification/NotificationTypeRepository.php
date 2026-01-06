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

        $title = $data['template']['title'] ?? null;
        $body  = $data['template']['body'] ?? null;

        $type->template_title = $this->normalizeTemplateContent($title);
        $type->template_body  = $this->normalizeTemplateContent($body);

        $type->placeholders = $data['placeholders'] ?? null;
    }

    private function normalizeTemplateContent(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Если уже начинается с <p> или вообще содержит блочную разметку – не трогаем
        if (preg_match('/^\s*<p[\s>]/i', $value) || str_contains($value, '<')) {
            return $value;
        }

        // Иначе оборачиваем в <p>...</p>
        return '<p>' . e($value) . '</p>';
    }
}
