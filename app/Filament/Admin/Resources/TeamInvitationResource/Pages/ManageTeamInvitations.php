<?php

namespace App\Filament\Admin\Resources\TeamInvitationResource\Pages;

use App\Filament\Admin\Resources\TeamInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTeamInvitations extends ManageRecords
{
    protected static string $resource = TeamInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
