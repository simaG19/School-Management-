<?php

namespace App\Filament\Resources;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\GradeResource\Pages;
use App\Filament\Resources\GradeResource\RelationManagers;
use App\Models\Grade;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;
    // This method is used to modify the query used in the resource table.

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If the logged-in user is a student, filter to only their grades.
        if (auth()->check() && auth()->user()->hasRole('student')) {
            $query->where('student_id', auth()->id());
        }


        return $query;
    }
    public static function canCreate(): bool
{
    return auth()->user()->hasRole('admin') || auth()->user()->hasRole('teacher');
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->hasRole('admin') || auth()->user()->hasRole('teacher');
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
                Select::make('subject_id')
                ->label('Subject')
                ->options(\App\Models\Subject::all()->pluck('name', 'id'))
                ->required(),
            Select::make('teacher_id')
                ->label('Teacher')
                ->options(\App\Models\User::role('teacher')->get()->pluck('name', 'id'))
                ->required(),
            Select::make('student_id')
                ->label('Student')
                ->options(\App\Models\User::role('student')->get()->pluck('name', 'id'))
                ->required(),
            TextInput::make('mark')
                ->numeric()
                ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('subject.name')
                ->label('Subject')
                ->sortable()
                ->searchable(),

            // Display the teacher's name
            TextColumn::make('teacher.name')
                ->label('Teacher')
                ->sortable()
                ->searchable(),

            // Display the student's name
            TextColumn::make('student.name')
                ->label('Student')
                ->sortable()
                ->searchable(),

            // Display the mark
            TextColumn::make('mark')
                ->label('Mark')
                ->sortable(),

            // Display the created at timestamp
            TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
