<?php

namespace App\Filament\Resources\UserNotificationResource\Pages;

use App\Filament\Infolists\Notification\ListDataEntry;
use App\Filament\Resources\NotificationCategoryResource;
use App\Filament\Resources\NotificationTypeResource;
use App\Filament\Resources\UserNotificationResource;
use App\Filament\Resources\UserResource;
use App\Models\UserNotification;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewUserNotification extends ViewRecord
{
    protected static string $resource = UserNotificationResource::class;

    public function getTitle(): string
    {
        return 'View Notification';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('User')
                            ->schema([
                                TextEntry::make('user.username')
                                    ->color('primary')
                                    ->url(
                                        fn (UserNotification $record) => UserResource::getUrl(
                                            'edit',
                                            ['record' => $record->user]
                                        )
                                    )
                                    ->openUrlInNewTab(),
                                TextEntry::make('user.email')
                                    ->icon('heroicon-m-envelope'),
                            ])
                            ->icon('heroicon-m-user'),

                        Section::make('Type')
                            ->schema([
                                TextEntry::make('type.name')
                                    ->color('info')
                                    ->url(
                                        fn (UserNotification $record) => NotificationTypeResource::getUrl(
                                            'edit',
                                            ['record' => $record->type]
                                        )
                                    )
                                    ->openUrlInNewTab(),
                                TextEntry::make('type.category.name')
                                    ->label('Category')
                                    ->color('success')
                                    ->url(
                                        fn (UserNotification $record) => NotificationCategoryResource::getUrl(
                                            'edit',
                                            ['record' => $record->type->category]
                                        )
                                    )
                                    ->openUrlInNewTab(),
                            ])
                            ->icon('heroicon-m-squares-2x2'),
                    ])
                    ->columnSpan(1),

                Group::make()
                    ->schema([
                        Section::make('Info')
                            ->schema([
                                TextEntry::make('title')
                                    ->html(),
                                TextEntry::make('body')
                                    ->html(),
                                TextEntry::make('read_at')
                                    ->since()
                                    ->dateTimeTooltip(),
                                TextEntry::make('created_at')
                                    ->since()
                                    ->dateTimeTooltip(),
                                TextEntry::make('deleted_at')
                                    ->since()
                                    ->dateTimeTooltip(),
                            ])
                            ->icon('heroicon-m-information-circle'),

                        Section::make('Data')
                            ->schema([
                                ListDataEntry::make('data'),
                            ])
                            ->icon('heroicon-m-code-bracket-square'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
