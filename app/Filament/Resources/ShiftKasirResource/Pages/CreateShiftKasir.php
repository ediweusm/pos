<?php

namespace App\Filament\Resources\ShiftKasirResource\Pages;

use App\Filament\Resources\ShiftKasirResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateShiftKasir extends CreateRecord
{
    protected static string $resource = ShiftKasirResource::class;

    protected function beforeCreate(): void
    {
        $user = auth()->user();
        if ($user && $user->hasOpenShift()) {
            Notification::make()
                ->title('Gagal Membuka Shift')
                ->body('Anda masih memiliki shift aktif yang berstatus OPEN. Silakan tutup shift tersebut terlebih dahulu.')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['cabang_id'] = $user->cabang_id;
        $data['gudang_id'] = $user->gudang_id;
        $data['waktu_buka'] = now();
        $data['status'] = 'OPEN';

        return $data;
    }
}
