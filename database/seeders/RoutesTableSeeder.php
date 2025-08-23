<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesTableSeeder extends Seeder
{
    public function run()
    {
        $routes = [
            ['prefix' => 'BAR', 'name' => 'BARBERENA', 'employee_id' => null, 'agency_id' => 6, 'plates' => '0'],
            ['prefix' => 'BAR2', 'name' => 'BARBERENA 2', 'employee_id' => null, 'agency_id' => 6, 'plates' => '0'],
            ['prefix' => 'CHI1', 'name' => 'CHIQUIMULA 1', 'employee_id' => null, 'agency_id' => 20, 'plates' => 'C-00000'],
            ['prefix' => 'CHI2', 'name' => 'CHI2', 'employee_id' => null, 'agency_id' => 20, 'plates' => '0'],
            ['prefix' => 'CHI3', 'name' => 'CHI3', 'employee_id' => null, 'agency_id' => 20, 'plates' => '0'],
            ['prefix' => 'CHINOC', 'name' => 'CHIQUIMULA NOCTURNO', 'employee_id' => null, 'agency_id' => 20, 'plates' => '0'],
            ['prefix' => 'CHM', 'name' => 'CHIMALTENANGO', 'employee_id' => null, 'agency_id' => 4, 'plates' => '0'],
            ['prefix' => 'CHM 2', 'name' => 'CHIMALTENANGO 2', 'employee_id' => null, 'agency_id' => 4, 'plates' => '0'],
            ['prefix' => 'ESC 1', 'name' => 'ESCUINTLA 1', 'employee_id' => null, 'agency_id' => 5, 'plates' => '0'],
            ['prefix' => 'ESC 2', 'name' => 'ESCUINTLA 2', 'employee_id' => null, 'agency_id' => 5, 'plates' => '0'],
            ['prefix' => 'GUS', 'name' => 'GUS', 'employee_id' => null, 'agency_id' => 2, 'plates' => '0'],
            ['prefix' => 'HUE', 'name' => 'HUEHUE', 'employee_id' => null, 'agency_id' => 13, 'plates' => '0'],
            ['prefix' => 'JAL1', 'name' => 'JALAPA 1', 'employee_id' => null, 'agency_id' => 21, 'plates' => 'C-872BHR'],
            ['prefix' => 'JAL2', 'name' => 'JALAPA 2', 'employee_id' => null, 'agency_id' => 21, 'plates' => 'C-872BHR'],
            ['prefix' => 'JUT1', 'name' => 'JUTIAPA 1', 'employee_id' => null, 'agency_id' => 22, 'plates' => 'C-572BRQ'],
            ['prefix' => 'JUT2', 'name' => 'JUTIAPA 2', 'employee_id' => null, 'agency_id' => 22, 'plates' => 'C-872BHR'],
            ['prefix' => 'JUT3', 'name' => 'JUTIAPA 3', 'employee_id' => null, 'agency_id' => 22, 'plates' => 'C-872BHR'],
            ['prefix' => 'JUTNOC', 'name' => 'JUT NOCTURNO', 'employee_id' => null, 'agency_id' => 22, 'plates' => '0'],
            ['prefix' => 'MAZ 1', 'name' => 'MAZ1', 'employee_id' => null, 'agency_id' => 10, 'plates' => '0'],
            ['prefix' => 'MAZ 2', 'name' => 'MAZ 2', 'employee_id' => null, 'agency_id' => 10, 'plates' => '0'],
            ['prefix' => 'PTB', 'name' => 'PUERTO BARRIOS', 'employee_id' => null, 'agency_id' => 18, 'plates' => '0'],
            ['prefix' => 'QCH', 'name' => 'QUICHE', 'employee_id' => null, 'agency_id' => 14, 'plates' => '0'],
            ['prefix' => 'R-1', 'name' => 'RUTA 1', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-804BYF'],
            ['prefix' => 'R-2', 'name' => 'RUTA 2', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-107BWN'],
            ['prefix' => 'R-3', 'name' => 'RUTA 3', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-805BYF'],
            ['prefix' => 'R-4', 'name' => 'RUTA 4', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-106BWN'],
            ['prefix' => 'R-5', 'name' => 'RUTA 5', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-610BZB'],
            ['prefix' => 'R-6', 'name' => 'RUTA 6', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-611BZB'],
            ['prefix' => 'R-7', 'name' => 'RUTA 7', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-179BYC'],
            ['prefix' => 'R8', 'name' => 'RUTA 8', 'employee_id' => null, 'agency_id' => 1, 'plates' => 'C-097BTV'],
            ['prefix' => 'REU', 'name' => 'RETALHULEU', 'employee_id' => null, 'agency_id' => 11, 'plates' => '0'],
            ['prefix' => 'REU 2', 'name' => 'RETALHULEU 2', 'employee_id' => null, 'agency_id' => 11, 'plates' => '0'],
            ['prefix' => 'REUNOC', 'name' => 'REU NOCTURNO', 'employee_id' => null, 'agency_id' => 11, 'plates' => '0'],
            ['prefix' => 'SMA', 'name' => 'SAN MARCOS', 'employee_id' => null, 'agency_id' => 12, 'plates' => '0'],
            ['prefix' => 'SOL', 'name' => 'SOLOLA', 'employee_id' => null, 'agency_id' => 7, 'plates' => '0'],
            ['prefix' => 'TOT', 'name' => 'TOTONICAPAN', 'employee_id' => null, 'agency_id' => 8, 'plates' => '0'],
            ['prefix' => 'XEL', 'name' => 'XEL', 'employee_id' => null, 'agency_id' => 9, 'plates' => 'C-181BRY'],
            ['prefix' => 'XEL 3', 'name' => 'XELA 3', 'employee_id' => null, 'agency_id' => 9, 'plates' => '0'],
            ['prefix' => 'XEL2', 'name' => 'XEL2', 'employee_id' => null, 'agency_id' => 9, 'plates' => '0'],
            ['prefix' => 'XELANOC', 'name' => 'XEL NOCTURNO', 'employee_id' => null, 'agency_id' => 9, 'plates' => '0'],
            ['prefix' => 'ZAC', 'name' => 'ZACAPA', 'employee_id' => null, 'agency_id' => 19, 'plates' => '0'],
            ['prefix' => 'ZAC2', 'name' => 'ZACAPA 2', 'employee_id' => null, 'agency_id' => 19, 'plates' => '0'],
        ];

        DB::table('routes')->insert($routes);
    }
}
