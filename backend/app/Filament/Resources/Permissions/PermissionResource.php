<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Resources\Permissions\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $modelLabel = 'Разрешение';
    protected static ?string $pluralModelLabel = 'Разрешения';

    public static function getNavigationGroup(): ?string
    {
        return 'Управление доступом';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-key';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Название разрешения'),

                Forms\Components\Hidden::make('guard_name')
                    ->default('web')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Название'),

                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->label('Guard'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Создано'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage permissions') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('manage permissions') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('manage permissions') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('manage permissions') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('manage permissions') ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
