<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->name = ucwords(strtolower($state));
                }),
            ImportColumn::make('last_name')
                ->requiredMapping()
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->last_name = ucwords(strtolower($state));
                }),
            ImportColumn::make('comments')
                ->requiredMapping()
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->comments = ucwords(strtolower($state));
                }),
            ImportColumn::make('entry_date')
                ->requiredMapping(),
            ImportColumn::make('id_agency')
                ->numeric(),
            ImportColumn::make('id_charge')
                ->numeric(),
            ImportColumn::make('bank_account')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('address')
                ->requiredMapping()
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->address = ucwords(strtolower($state));
                }),
            ImportColumn::make('town_id')
                ->numeric(),
            ImportColumn::make('departament_id')
                ->numeric(),
            ImportColumn::make('zone')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('birthplace')
                ->requiredMapping(),
            ImportColumn::make('phone')
                ->requiredMapping(),
            ImportColumn::make('cellphone')
                ->requiredMapping(),
            ImportColumn::make('birth_date')
                ->requiredMapping(),
            ImportColumn::make('age')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('civil_status_id')
                ->numeric(),
            ImportColumn::make('gender_id')
                ->numeric(),
            ImportColumn::make('children')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('dpi')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('nit')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('email')
                ->requiredMapping()
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->email = strtolower($state);
                }),
            ImportColumn::make('photo')
                ->requiredMapping(),
            ImportColumn::make('nationality_id')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('status_id')
                ->numeric(),
            ImportColumn::make('id_payroll')
                ->numeric(),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        // return Employee::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Employee();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your employee import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
