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
                ->rules(['required', 'max:25'])
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->name = ucwords(strtolower($state));
                }),
            ImportColumn::make('last_name')
                ->requiredMapping()
                ->rules(['required', 'max:25'])
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->last_name = ucwords(strtolower($state));
                }),
            ImportColumn::make('comments')
                ->requiredMapping()
                ->rules(['required'])
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->comments = ucwords(strtolower($state));
                }),
            ImportColumn::make('entry_date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('id_agency')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('id_charge')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('bank_account')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:500'])
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->address = ucwords(strtolower($state));
                }),
            ImportColumn::make('town_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('departament_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('zone')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('birthplace')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:15']),
            ImportColumn::make('cellphone')
                ->requiredMapping()
                ->rules(['required', 'max:15']),
            ImportColumn::make('birth_date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('age')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('civil_status_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('gender_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('children')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('dpi')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('nit')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:100'])
                ->fillRecordUsing(function (Employee $record, string $state): void {
                    $record->email = strtolower($state);
                }),
            ImportColumn::make('photo')
                ->requiredMapping()
                ->rules(['required', 'max:191']),
            ImportColumn::make('nationality_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('status_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('id_payroll')
                ->numeric()
                ->rules(['integer']),
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
