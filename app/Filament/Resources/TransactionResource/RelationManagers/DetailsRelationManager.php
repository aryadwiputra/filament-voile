<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->required()
                    ->relationship('product', 'name')
                    ->preload()
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Mengambil harga produk berdasarkan product_id yang dipilih
                        if ($state) {
                            $product = \App\Models\Product::find($state);
                            if ($product) {
                                $set('price_product', $product->price);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->placeholder('Quantity')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, callable $get) {
                        // Menghitung total harga saat quantity berubah
                        $price = $get('price_product');
                        if ($price && $state) {
                            $set('price', $price * $state);
                        }
                    }),
                Forms\Components\TextInput::make('price_product')
                    ->required()
                    ->placeholder('Price Product')
                    ->numeric()
                    ->reactive()
                    ->disabled()
                    ->afterStateUpdated(function (callable $set, $state, callable $get) {
                        // Menghitung total harga saat price berubah
                        $quantity = $get('quantity');
                        if ($quantity && $state) {
                            $set('price', $state * $quantity);
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('details')
            ->columns([
                Tables\Columns\ImageColumn::make('product.thumbnail')->circular(),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

    // Before create
    protected function beforeCreate(): void
    {
        //
    }
}
