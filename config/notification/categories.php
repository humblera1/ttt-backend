<?php

use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;

return [
    [
        'key' => NotificationCategory::System,
        'name' => NotificationCategory::label(NotificationCategory::System),
        'description' => 'Важные новости и изменения платформы: обновления, технические работы, массовые объявления.',
        'types' => [
            [
                'key' => NotificationType::SystemAnnouncement,
                'name' => NotificationType::label(NotificationType::SystemAnnouncement),
                'description' => 'Массовое объявление для всех или части пользователей: новости, изменения, технические работы.',
                'placeholders' => [
                    // текст задаёт администратор
                ],
            ],
            [
                'key' => NotificationType::AdminMessage,
                'name' => NotificationType::label(NotificationType::AdminMessage),
                'description' => 'Персональное сообщение от администратора конкретному пользователю или группе.',
                'placeholders' => [
                    [
                        'key' => 'admin_name',
                        'label' => 'Имя администратора',
                        'description' => 'Имя администратора, отправившего сообщение.',
                    ],
                ],
                'template' => [
                    'title' => 'Сообщение от администратора',
                    'body' => 'Администратор {{admin_name}} отправил вам сообщение.',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::Subscription,
        'name' => NotificationCategory::label(NotificationCategory::Subscription),
        'description' => 'Все события по премиум-доступу: активация, продление, истечение, успешные и неуспешные платежи, возвраты.',
        'types' => [
            [
                'key' => NotificationType::SubscriptionActivated,
                'name' => NotificationType::label(NotificationType::SubscriptionActivated),
                'description' => 'Уведомление о том, что премиальная подписка активирована.',
                'placeholders' => [
                    [
                        'key' => 'plan_name',
                        'label' => 'Название тарифа',
                        'description' => 'Например, «Pro», «Team» и т. п.',
                    ],
                    [
                        'key' => 'expires_at',
                        'label' => 'Дата истечения',
                        'description' => 'Дата окончания подписки в формате ДД.ММ.ГГГГ или ISO.',
                    ],
                ],
                'template' => [
                    'title' => 'Подписка {{plan_name}} активирована',
                    'body'  => 'Ваша подписка {{plan_name}} активна до {{expires_at}}.',
                ],
            ],
            [
                'key' => NotificationType::SubscriptionExpiring,
                'name' => NotificationType::label(NotificationType::SubscriptionExpiring),
                'description' => 'Напоминание о скором истечении подписки.',
                'placeholders' => [
                    [
                        'key' => 'plan_name',
                        'label' => 'Название тарифа',
                        'description' => 'Название текущего тарифного плана.',
                    ],
                    [
                        'key' => 'expires_at',
                        'label' => 'Дата истечения',
                        'description' => 'Дата окончания подписки.',
                    ],
                    [
                        'key' => 'days_left',
                        'label' => 'Оставшееся количество дней',
                        'description' => 'Количество дней до истечения подписки.',
                    ],
                ],
                'template' => [
                    'title' => 'Подписка {{plan_name}} скоро истечет',
                    'body'  => 'Подписка истекает {{expires_at}} (осталось {{days_left}} дн.).',
                ],
            ],
            [
                'key' => NotificationType::SubscriptionExpired,
                'name' => NotificationType::label(NotificationType::SubscriptionExpired),
                'description' => 'Уведомление о том, что подписка более не активна.',
                'placeholders' => [
                    [
                        'key' => 'expired_at',
                        'label' => 'Дата истечения',
                        'description' => 'Дата, когда подписка перестала действовать.',
                    ],
                ],
                'template' => [
                    'title' => 'Подписка истекла',
                    'body'  => 'Подписка истекла {{expired_at}}.',
                ],
            ],
            [
                'key' => NotificationType::PaymentSuccessful,
                'name' => NotificationType::label(NotificationType::PaymentSuccessful),
                'description' => 'Уведомление об успешной оплате подписки или продления.',
                'placeholders' => [
                    [
                        'key' => 'amount',
                        'label' => 'Сумма платежа',
                        'description' => 'Сумма, списанная с пользователя.',
                    ],
                    [
                        'key' => 'currency',
                        'label' => 'Валюта',
                        'description' => 'Валюта платежа (например, RUB, USD).',
                    ],
                    [
                        'key' => 'plan_name',
                        'label' => 'Название тарифа',
                        'description' => 'Название тарифного плана, за который был произведен платеж.',
                    ],
                ],
                'template' => [
                    'title' => 'Платеж успешен',
                    'body'  => 'Платеж на сумму {{amount}} {{currency}} за тариф {{plan_name}} успешно выполнен.',
                ],
            ],
            [
                'key' => NotificationType::PaymentFailed,
                'name' => NotificationType::label(NotificationType::PaymentFailed),
                'description' => 'Уведомление о неуспешной оплате (ошибка карты, отклонение банком и т. п.).',
                'placeholders' => [
                    [
                        'key' => 'reason',
                        'label' => 'Причина отказа',
                        'description' => 'Краткое описание причины неуспешного платежа, если доступно.',
                    ],
                ],
                'template' => [
                    'title' => 'Платеж не прошел',
                    'body'  => 'Не удалось списание: {{reason}}.',
                ],
            ],
            [
                'key' => NotificationType::PaymentRefunded,
                'name' => NotificationType::label(NotificationType::PaymentRefunded),
                'description' => 'Уведомление о возврате средств за подписку или покупку.',
                'placeholders' => [
                    [
                        'key' => 'amount',
                        'label' => 'Сумма возврата',
                        'description' => 'Сумма, возвращенная пользователю.',
                    ],
                    [
                        'key' => 'currency',
                        'label' => 'Валюта',
                        'description' => 'Валюта возврата.',
                    ],
                ],
                'template' => [
                    'title' => 'Возврат платежа',
                    'body'  => 'Возвращено {{amount}} {{currency}}.',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::Proposal,
        'name' => NotificationCategory::label(NotificationCategory::Proposal),
        'description' => 'Статусы и действия по предложенным пользователями вопросам: получено, на модерации, принято, отклонено, запрошены правки.',
        'types' => [
            [
                'key' => NotificationType::ProposalReceived,
                'name' => NotificationType::label(NotificationType::ProposalReceived),
                'description' => 'Подтверждение, что предложенный вопрос успешно получен системой.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Краткий заголовок предложенного вопроса.',
                    ],
                ],
                'template' => [
                    'title' => 'Предложение получено',
                    'body'  => 'Ваш вопрос «{{question_title}}» получен и будет рассмотрен.',
                ],
            ],
            [
                'key' => NotificationType::ProposalUnderReview,
                'name' => NotificationType::label(NotificationType::ProposalUnderReview),
                'description' => 'Уведомление о том, что вопрос передан на рассмотрение модераторам.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Краткий заголовок предложенного вопроса.',
                    ],
                ],
                'template' => [
                    'title' => 'Предложение на модерации',
                    'body'  => 'Ваш вопрос «{{question_title}}» находится на модерации.',
                ],
            ],
            [
                'key' => NotificationType::ProposalAccepted,
                'name' => NotificationType::label(NotificationType::ProposalAccepted),
                'description' => 'Уведомление о том, что предложенный вопрос принят и опубликован в каталоге.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, который был принят.',
                    ],
                    [
                        'key' => 'question_id',
                        'label' => 'ID вопроса',
                        'description' => 'Идентификатор опубликованного вопроса.',
                    ],
                ],
                'template' => [
                    'title' => 'Предложение принято',
                    'body'  => 'Вопрос «{{question_title}}» опубликован (ID: {{question_id}}).',
                ],
            ],
            [
                'key' => NotificationType::ProposalRejected,
                'name' => NotificationType::label(NotificationType::ProposalRejected),
                'description' => 'Уведомление о том, что предложенный вопрос отклонен модерацией.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, который был отклонен.',
                    ],
                    [
                        'key' => 'rejection_reason',
                        'label' => 'Причина отклонения',
                        'description' => 'Краткая причина, по которой вопрос отклонен (например, дубликат, низкое качество и т. п.).',
                    ],
                ],
                'template' => [
                    'title' => 'Предложение отклонено',
                    'body'  => 'Вопрос «{{question_title}}» отклонен: {{rejection_reason}}.',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::ContentInteraction,
        'name' => NotificationCategory::label(NotificationCategory::ContentInteraction),
        'description' => 'Реакции и активность: лайки/дизлайки, новые комментарии, ответы/упоминания, добавление в избранное, публикация новых премиальных вопросов.',
        'types' => [
            [
                'key' => NotificationType::QuestionLiked,
                'name' => NotificationType::label(NotificationType::QuestionLiked),
                'description' => 'Уведомление автору, что его вопросу поставили ла��к.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, который получил лайк.',
                    ],
                    [
                        'key' => 'current_likes',
                        'label' => 'Текущее число лайков',
                        'description' => 'Общее количество лайков у вопроса после реакции.',
                    ],
                ],
                'template' => [
                    'title' => 'Ваш вопрос понравился пользователю',
                    'body'  => 'Вопрос «{{question_title}}» получил лайк. Всего лайков: {{current_likes}}.',
                ],
            ],
            [
                'key' => NotificationType::QuestionDisliked,
                'name' => NotificationType::label(NotificationType::QuestionDisliked),
                'description' => 'Уведомление автору, что его вопросу поставили дизлайк.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, который получил дизлайк.',
                    ],
                    [
                        'key' => 'current_dislikes',
                        'label' => 'Текущее число дизлайков',
                        'description' => 'Общее количество дизлайков у вопроса после реакции.',
                    ],
                ],
                'template' => [
                    'title' => 'Ваш вопрос получил дизлайк',
                    'body'  => 'Вопрос «{{question_title}}» получил дизлайк. Всего дизлайков: {{current_dislikes}}.',
                ],
            ],
            [
                'key' => NotificationType::NewComment,
                'name' => NotificationType::label(NotificationType::NewComment),
                'description' => 'Уведомление автору вопроса о новом комментарии.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, к которому оставили комментарий.',
                    ],
                    [
                        'key' => 'comment_excerpt',
                        'label' => 'Фрагмент комментария',
                        'description' => 'Короткий фрагмент текста комментария.',
                    ],
                    [
                        'key' => 'comment_author',
                        'label' => 'Автор комментария',
                        'description' => 'Имя или никнейм пользователя, оставившего комментарий.',
                    ],
                ],
                'template' => [
                    'title' => 'Новый комментарий',
                    'body'  => 'К вопросу «{{question_title}}» оставлен комментарий: «{{comment_excerpt}}» — {{comment_author}}.',
                ],
            ],
            [
                'key' => NotificationType::CommentReply,
                'name' => NotificationType::label(NotificationType::CommentReply),
                'description' => 'Уведомление пользователю, что кто-то ответил на его комментарий.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, к которому относятся комментарии.',
                    ],
                    [
                        'key' => 'reply_excerpt',
                        'label' => 'Фрагмент ответа',
                        'description' => 'Короткий фрагмент текста ответа.',
                    ],
                    [
                        'key' => 'reply_author',
                        'label' => 'Автор ответа',
                        'description' => 'Имя или никнейм пользователя, оставившего ответ.',
                    ],
                ],
                'template' => [
                    'title' => 'Новый ответ на ко��ментарий',
                    'body'  => 'К вопросу «{{question_title}}» новый ответ: «{{reply_excerpt}}» — {{reply_author}}.',
                ],
            ],
            [
                'key' => NotificationType::QuestionFavorited,
                'name' => NotificationType::label(NotificationType::QuestionFavorited),
                'description' => 'Уведомление автору, что другой пользователь добавил его вопрос в избранное.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок вопроса',
                        'description' => 'Заголовок вопроса, который добавили в избранное.',
                    ],
                ],
                'template' => [
                    'title' => 'Вопрос в избранном',
                    'body'  => 'Ваш вопрос «{{question_title}}» добавлен в избранное.',
                ],
            ],
            [
                'key' => NotificationType::NewPremiumQuestion,
                'name' => NotificationType::label(NotificationType::NewPremiumQuestion),
                'description' => 'Уведомление премиальным пользователям о появлении нового премиального вопроса.',
                'placeholders' => [
                    [
                        'key' => 'question_title',
                        'label' => 'Заголовок премиального вопроса',
                        'description' => 'Краткий заголовок нового премиального вопроса.',
                    ],
                    [
                        'key' => 'company_name',
                        'label' => 'Компания',
                        'description' => 'Название компании, к которой относится вопрос (если применимо).',
                    ],
                ],
                'template' => [
                    'title' => 'Новый премиальный вопрос',
                    'body'  => 'Новый премиальный вопрос: «{{question_title}}» в компании «{{company_name}}».',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::Moderation,
        'name' => NotificationCategory::label(NotificationCategory::Moderation),
        'description' => 'Предупреждения и действия модерации: скрытие/удаление вопросов или комментариев, причины отклонения, ограничения функционала, блокировки/разблокировки.',
        'types' => [
            [
                'key' => NotificationType::CommentRemoved,
                'name' => NotificationType::label(NotificationType::CommentRemoved),
                'description' => 'Уведомление пользователю, что его комментарий удален модераторами.',
                'placeholders' => [
                    [
                        'key' => 'comment_excerpt',
                        'label' => 'Фрагмент комментария',
                        'description' => 'Короткий фрагмент удаленного комментария.',
                    ],
                    [
                        'key' => 'moderation_reason',
                        'label' => 'Причина модерации',
                        'description' => 'Причина удаления комментария.',
                    ],
                ],
                'template' => [
                    'title' => 'Комментарий удален',
                    'body'  => 'Ваш комментарий удален: «{{comment_excerpt}}». Причина: {{moderation_reason}}.',
                ],
            ],
            [
                'key' => NotificationType::UserBanned,
                'name' => NotificationType::label(NotificationType::UserBanned),
                'description' => 'Уведомление о блокировке аккаунта (если показывается в пределах приложения).',
                'placeholders' => [
                    [
                        'key' => 'ban_reason',
                        'label' => 'Причина блокировки',
                        'description' => 'Причина блокировки акк��унта.',
                    ],
                ],
                'template' => [
                    'title' => 'Аккаунт заблокирован',
                    'body'  => 'Аккаунт заблокирован. Причина: {{ban_reason}}.',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::Account,
        'name' => NotificationCategory::label(NotificationCategory::Account),
        'description' => 'Изменения и события аккаунта: смена email/пароля, вход с нового устройства, включение/отключение 2FA, подтверждение почты.',
        'types' => [
            [
                'key' => NotificationType::EmailChanged,
                'name' => NotificationType::label(NotificationType::EmailChanged),
                'description' => 'Уведомление о смене адреса электронной почты аккаунта.',
                'placeholders' => [
                    [
                        'key' => 'old_email',
                        'label' => 'Старый email',
                        'description' => 'Предыдущий адрес электронной почты.',
                    ],
                    [
                        'key' => 'new_email',
                        'label' => 'Новый email',
                        'description' => 'Новый адрес электронной почты.',
                    ],
                ],
                'template' => [
                    'title' => 'Email изменен',
                    'body'  => 'Email изменен с {{old_email}} на {{new_email}}.',
                ],
            ],
            [
                'key' => NotificationType::PasswordChanged,
                'name' => NotificationType::label(NotificationType::PasswordChanged),
                'description' => 'Уведомление о том, что пароль аккаунта был изменен.',
                'placeholders' => [
                    [
                        'key' => 'changed_at',
                        'label' => 'Дата изменения',
                        'description' => 'Дата и время изменения пароля.',
                    ],
                ],
                'template' => [
                    'title' => 'Пароль изменен',
                    'body'  => 'Пароль вашего аккаунта изменен {{changed_at}}.',
                ],
            ],
            [
                'key' => NotificationType::NewLogin,
                'name' => NotificationType::label(NotificationType::NewLogin),
                'description' => 'Уведомление о входе в аккаунт с нового устройства или из нового расположения.',
                'placeholders' => [
                    [
                        'key' => 'ip_address',
                        'label' => 'IP-адрес',
                        'description' => 'IP-адрес, с которого был выполнен вход.',
                    ],
                    [
                        'key' => 'user_agent',
                        'label' => 'Устройство/браузер',
                        'description' => 'И��формация об устройстве/браузере.',
                    ],
                ],
                'template' => [
                    'title' => 'Новый вход',
                    'body'  => 'Новый вход: IP {{ip_address}}, устройство {{user_agent}}.',
                ],
            ],
        ],
    ],

    [
        'key' => NotificationCategory::Custom,
        'name' => NotificationCategory::label(NotificationCategory::Custom),
        'description' => 'Любые сообщения, не попадающие в стандартные категории, в том числе вручную отправленные администратором.',
        'types' => [
            [
                'key' => NotificationType::CustomMessage,
                'name' => NotificationType::label(NotificationType::CustomMessage),
                'description' => 'Гибкое уведомление, текст которого полностью задается администратором.',
                'placeholders' => [
                    // без плейсхолдеров - текст целиком задается вручную
                ],
            ],
        ],
    ],
];
