<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\Admin;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Info')
                    ->schema([

                        TextInput::make('name')
                            ->rules(REQUIRED_STRING_VALIDATION),

                        TextInput::make('email')
                            ->email()
                            ->rules(REQUIRED_EMAIL_VALIDATION),

                        TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->revealable()
                            ->dehydrateStateUsing(static fn(null|string $state): null|string => filled($state) ? Hash::make($state) : null,
                            )->required(static fn(Page $livewire): bool => $livewire instanceof CreateUser,
                            )->dehydrated(static fn(null|string $state): bool => filled($state),
                            )->label(static fn(Page $livewire): string => ($livewire instanceof EditUser) ? 'New Password' : 'Password'
                            ),

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple(),

                        Select::make('permissions')
                            ->relationship('permissions', 'name')
                            ->multiple()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name'),
                TextColumn::make('email'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
