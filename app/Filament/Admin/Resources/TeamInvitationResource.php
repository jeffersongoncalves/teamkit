<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeamInvitationResource\Pages;
use App\Filament\Infolists\AdditionalInformation;
use App\Models\Team;
use App\Models\TeamInvitation;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class TeamInvitationResource extends Resource
{
    protected static ?string $model = TeamInvitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'email';

    public static function getGloballySearchableAttributes(): array
    {
        return ['email'];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }

    public static function getModelLabel(): string
    {
        return __('Team Invitation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Team Invitations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Team Invitations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Cache::rememberForever('team_invitations_count', fn () => TeamInvitation::query()->count());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name')
                    ->live(onBlur: true)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique('team_invitations', 'email', modifyRuleUsing: fn ($rule, Forms\Get $get) => $rule->where('team_id', $get('team_id')))
                    ->required()
                    ->rules([fn (Forms\Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get) {
                        $team = Team::find($get('team_id'));
                        if ($team->users()->where('email', $value)->exists()) {
                            $fail(__('The email has already been taken.'));
                        }
                        if ($team->owner()->where('email', $value)->exists()) {
                            $fail(__('The email has already been taken.'));
                        }
                    }]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->columns()
                    ->schema([
                        Infolists\Components\TextEntry::make('team.name')
                            ->label('Team'),
                        Infolists\Components\TextEntry::make('email'),
                    ]),
                AdditionalInformation::make([
                    'created_at',
                    'updated_at',
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label(__('Team'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTeamInvitations::route('/'),
        ];
    }
}
