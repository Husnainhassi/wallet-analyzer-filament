<?php

namespace App\Filament\Resources;

use App\Filament\Imports\WalletImporter;
use App\Filament\Resources\WalletResource\Pages;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup as TableActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Validation\Rules\File;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationGroup = 'Analyzed Wallets';

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'Wallets';

    protected static ?string $modelLabel = 'Wallets';

    protected static ?string $slug = 'wallets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->maxLength(191)
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->maxLength(191)
                    ->required(),
                Forms\Components\TextInput::make('roi')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('winrate')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'normal'        => 'Normal',
                        'approved'      => 'Approved',
                        'in_review'     => 'Under Review',
                        'disqualified'  => 'Disqualified',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('winrate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gmgn_link')
                    ->label('Goto GMGN')
                    ->state(function () {
                        return 'View on GMGN';
                    })
                    ->url(function ($record) {
                        return "https://gmgn.ai/sol/address/{$record->address}";
                    })
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->icon('heroicon-o-arrow-top-right-on-square'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ImportAction::make()  // New import action
                    ->importer(WalletImporter::class)
                    ->fileRules([ 
                        File::types(['csv', 'xlsx'])->max(1024),
                    ])
                    ->label('Import')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                TableActionGroup::make([
                    Action::make('updateStatus')
                        ->form([
                            Select::make('status')
                                ->options([
                                    'normal' => 'Normal',
                                    'approved' => 'Approved',
                                    'in_review' => 'Under Review',
                                    'disqualified' => 'Disqualified',
                                ])
                        ])
                        ->action(function (array $data, $record) {
                            $record->update(['status' => $data['status']]);
                        })
                        ->icon('heroicon-o-pencil')
                        ->tooltip('Change Status'),
                ])
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
