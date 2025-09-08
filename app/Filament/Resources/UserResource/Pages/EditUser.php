<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\api\v1\UserBanService;
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
                               ->maxLength(255),

                           TextInput::make('first_name')
                               ->required()
                               ->maxLength(255),

                           TextInput::make('last_name')
                               ->required()
                               ->maxLength(255),

                           TextInput::make('password')
                               ->password()
                               ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                               ->required(fn ($context) => $context === 'create')
                               ->maxLength(255)
                               ->label('Password')
                       ]),
               ]),
       ];

       if ($canAssignRole) {
           $schema[] = Section::make('Roles')
                       ->schema([
                           Select::make('roles')
                               ->multiple()
                               ->relationship('roles', 'name')
                               ->preload()
                               ->options(
                                   Role::all()->pluck('name', 'id')->toArray()
                               )
                               ->disableOptionWhen(
                               // Нельзя снять у себя роль администратора (если редактируешь себя)
                                   fn ($roleId) =>
                                       $currentUser->id === $user->id
                                       && Role::find($roleId)?->name === 'admin'
                               )
                               ->hint('You cannot remove your own admin role.')
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
                ->color('danger')
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
