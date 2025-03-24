<?php

namespace App\Filament\Resources;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    if (auth()->check() && auth()->user()->hasRole('student')) {
        // A student should only see their own user record.
        $query->where('id', auth()->id());
    }

    return $query;
}

public static function canCreate(): bool
{
    return auth()->user()->hasRole('admin');
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->hasRole('admin');
}

public static function canDelete(Model $record): bool
{
    return auth()->user()->hasRole('admin');
}

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name'),
                TextInput::make('password')
                    ->password()
                    ->required() // Make it required or optional as per your logic
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
