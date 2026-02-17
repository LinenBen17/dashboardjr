<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // ✅ Importa Log

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buscar en payrolls
        $payroll = DB::table('payrolls')
            ->select('id', 'state')
            ->where('state', 'like', '%dentro%') // ✅ where + like correcto
            ->first();

        // Insertar en la tabla 'users', no 'user'
        DB::table('users')->insert([ // ✅ insert, no "inseryt"
            'name' => 'masterpeace',
            'email' => 'mesquite275@gmail.com', // ✅ corrige el email
            'password' => Hash::make('021ManEse*'), // ✅ Hash importado
        ]);

        // Mostrar ID en el log
        if ($payroll) {
            Log::info('ID del payroll encontrado: ' . $payroll->id);
        } else {
            Log::warning('No se encontró ningún payroll con estado "dentro"');
        }
    }
}