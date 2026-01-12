<?php

namespace App\Entities\Notification;

use App\Models\NotificationType;

class NotificationRenderer
{
    public function render(NotificationType $type, array $data): array
    {
        $title = $this->interpolate($type->template_title, $data);
        $body = $this->interpolate($type->template_body, $data);

        return [$title, $body];
    }

    /**
     * Interpolates into the string {{key}} => value
     */
    private function interpolate(string $template, array $data): string
    {
        $replacements = [];

        foreach ($data as $key => $value) {
            $replacements['{{'.$key.'}}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}
