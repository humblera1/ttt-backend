<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\api\v1\UserBanService;
use Closure;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Enums\Role as RoleEnum;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

   public function form(Form $form): Form
   {
       $user = $this->record;
       $currentUser = auth()->user();

       $canAssignRole = $currentUser->can('assign-role', $user);

       $schema = [
           Section::make('Main Info')
               ->schema([
                   Grid::make()
                       ->schema([
                           TextInput::make('username')
                               ->required()
                               ->maxLength(255)
                                ->columnSpan(2),

                           TextInput::make('first_name')
                               ->maxLength(255),

                           TextInput::make('last_name')
                               ->maxLength(255),
                       ]),
               ]),
           Section::make('Password')
                ->schema([
                    Grid::make()
                        ->schema([
                            TextInput::make('password')
                                ->password()
                                ->confirmed()
                                ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                                ->required(fn ($context) => $context === 'create')
                                ->maxLength(255)
                                ->label('Password'),
                            TextInput::make('password_confirmation')
                                ->password()
                                ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                                ->required(fn ($context) => $context === 'create')
                                ->maxLength(255)
                                ->label('Password Again'),
                        ]),
                ])
       ];

       if ($canAssignRole) {
           $schema[] = Section::make('Role')
                       ->schema([
                           Select::make('role')
                               ->required()
                               ->relationship('roles', 'name')
                               ->preload()
                               ->options(
                                   Role::all()->pluck('name', 'id')->toArray()
                               )
                               ->hint('You cannot remove admin role.')
                               ->rules([
                                   fn (): Closure => function (string $attribute, int $value, Closure $fail) {
                                       $user = $this->record;
                                       $adminRoleId = Role::where('name', RoleEnum::Admin->value)->value('id') ?? null;

                                       $hadAdminRole = $user->hasRole(RoleEnum::Admin->value);
                                       $newRoleIsNotAdmin = $value != $adminRoleId;

                                       if ($hadAdminRole && $newRoleIsNotAdmin) {
                                           $fail('You cannot remove admin role!');
                                       }
                                   },
                               ]),
                       ]);
       }

       return $form->schema($schema);
   }

    protected function getHeaderActions(): array
    {
        $user = $this->record;
        $currentUser = auth()->user();

        $actions = [];

        if ($currentUser->can('delete', $user)) {
            $actions[] = Actions\DeleteAction::make();
        }

        if ($currentUser->can('ban', $user)) {
            $actions[] = Actions\Action::make('ban')
                ->label('Ban')
                ->color('warning')
                ->action(function () use ($user) {
                    $banService = app(UserBanService::class);

                    $banService->ban($user);

                    Notification::make()
                        ->title('User has been banned.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation();
        }

        return $actions;
    }
}
