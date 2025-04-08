<?php

namespace Database\Seeders;

use App\Filament\Imports\DepartamentImporter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class Departament extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta del archivo Excel
        $path = storage_path('app/imports/departaments.csv');

        // Simula un archivo subido
        $uploadedFile = new UploadedFile(
            $path,
            'departaments.csv',
            null,
            null,
            true
        );

        // Crea una instancia del archivo importado
        $importFile = ImportFile::make($uploadedFile);

        // Ejecuta el importador
        DepartamentImporter::make()->import($importFile);
    }
}
