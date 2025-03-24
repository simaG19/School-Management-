<?php

namespace App\Filament\Resources;
use Illuminate\Database\Eloquent\Model;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Models\User;
class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                //
                Select::make('student_id')
                ->label('Student')
                ->options(User::role('student')->pluck('name', 'id'))
                ->required(),
                Select::make('teacher_id')
                ->label('Teacher')
                ->options(User::role('teacher')->pluck('name', 'id'))
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'present' => 'Present',
                    'absent' => 'Absent',
                    'late' => 'Late',
                ])
                ->required(),

            DatePicker::make('date')
                ->label('Date')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('student.name')->label('Student'),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'late',
                    ]),
                TextColumn::make('date'),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
