<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Imports\WalletsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

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
                Forms\Components\TextInput::make('address')
                    ->maxLength(191)
                    ->default(null),
                Forms\Components\TextInput::make('roi')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('win_rate')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('win_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
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
                Tables\Actions\EditAction::make(),
                Action::make('import')
                    ->label('Import Wallets')
                    ->icon('heroicon-o-upload')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Excel File')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel']),
                    ])
                    ->action(function (array $data): void {
                        $file = $data['file'];
                        Excel::import(new WalletsImport, $file);

                        Notification::make()
                            ->title('Wallets imported successfully!')
                            ->success()
                            ->send();
                    }),
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
