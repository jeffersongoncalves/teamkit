<?php

namespace App\Filament\App\Pages;

use App\Models\TeamInvitation;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class TeamInvitationAccept extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.team-invitation-accept';


    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->query(TeamInvitation::query()->where('email', auth('web')->user()->email))
            ->columns([
                TextColumn::make('team.name')
                    ->label(__('Team'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('accept')
                    ->icon("heroicon-o-check")
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalIcon("heroicon-o-check")
                    ->modalHeading(__('Accept invitation?'))
                    ->action(function (TeamInvitation $record) {
                        $record->accept(auth()->user());

                        Notification::make()
                            ->title(__('Invitation accepted!'))
                            ->success()
                            ->send();

                        redirect()->route('filament.app.pages.dashboard', ['tenant' => $record->team_id]);
                    }),
                Action::make('cancel')
                    ->color('danger')
                    ->icon("heroicon-o-x-mark")
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalIcon("heroicon-o-x-mark")
                    ->modalHeading(__('Cancel invitation?'))
                    ->action(function (TeamInvitation $record) {
                        $record->delete();

                        Notification::make()
                            ->title(__('Invitation canceled!'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
