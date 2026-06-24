<?php

namespace App\Providers;

use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Pages;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Illuminate\Support\ServiceProvider;

/**
 * Kit-specific Filament customizations that are NOT covered by the
 * jeffersongoncalves/filament-sensible-defaults plugin (which is auto-applied
 * and provides the baseline action/form/table/infolist defaults).
 *
 * Only the non-default tweaks remain here: Brazilian display formats, sticky
 * form actions, and the Filament 3 table-action / form-action / infolist-action
 * defaults that the plugin does not configure. These boot after the plugin, so
 * they intentionally win where they overlap.
 */
class FilamentServiceProvider extends ServiceProvider
{
    private string $defaultDateDisplayFormat = 'd/m/Y';

    private string $defaultDateTimeDisplayFormat = 'd/m/Y H:i:s';

    private string $defaultCurrency = 'brl';

    public function boot(): void
    {
        $this->configureActions();
        $this->configureForms();
        $this->configureInfolists();
        $this->configurePages();
        $this->configureTables();
    }

    private function configureActions(): void
    {
        Actions\StaticAction::configureUsing(function (Actions\StaticAction $staticAction) {
            return $staticAction->translateLabel();
        });
    }

    private function configureForms(): void
    {
        Forms\Components\Actions\Action::configureUsing(function (Forms\Components\Actions\Action $action) {
            return $action->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false);
        });

        Forms\Components\Placeholder::configureUsing(function (Forms\Components\Placeholder $component) {
            return $component->columnSpanFull();
        });

        Forms\Components\RichEditor::configureUsing(function (Forms\Components\RichEditor $component) {
            return $component->disableToolbarButtons(['blockquote']);
        });
    }

    private function configureInfolists(): void
    {
        Infolists\Infolist::$defaultDateDisplayFormat = $this->defaultDateDisplayFormat;

        Infolists\Infolist::$defaultDateTimeDisplayFormat = $this->defaultDateTimeDisplayFormat;

        Infolists\Infolist::$defaultCurrency = $this->defaultCurrency;

        Infolists\Components\Actions\Action::configureUsing(function (Infolists\Components\Actions\Action $action) {
            return $action->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false);
        });
    }

    private function configurePages(): void
    {
        Pages\Page::$formActionsAreSticky = true;
    }

    private function configureTables(): void
    {
        Tables\Table::$defaultDateDisplayFormat = $this->defaultDateDisplayFormat;

        Tables\Table::$defaultDateTimeDisplayFormat = $this->defaultDateTimeDisplayFormat;

        Tables\Table::$defaultCurrency = $this->defaultCurrency;

        Tables\Actions\Action::configureUsing(function (Tables\Actions\Action $action) {
            return $action->modalWidth(MaxWidth::Medium)
                ->closeModalByClickingAway(false);
        });

        Tables\Actions\CreateAction::configureUsing(function (Tables\Actions\CreateAction $action) {
            return $action->createAnother(false);
        });

        Tables\Actions\ViewAction::configureUsing(function (Tables\Actions\ViewAction $action) {
            return $action->hiddenLabel()
                ->button();
        });

        Tables\Actions\EditAction::configureUsing(function (Tables\Actions\EditAction $action) {
            return $action->hiddenLabel()
                ->button();
        });

        Tables\Actions\DeleteAction::configureUsing(function (Tables\Actions\DeleteAction $action) {
            return $action->hiddenLabel()
                ->button();
        });
    }
}
