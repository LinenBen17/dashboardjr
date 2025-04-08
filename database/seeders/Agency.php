<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Agency extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('agencies')->truncate();
        DB::table('agencies')->insert([
            ['departament_id' => 1, 'name' => 'Guatemala Centro', 'short' => null,],
            ['departament_id' => 2, 'name' => 'El Progreso Centro', 'short' => null,],
            ['departament_id' => 3, 'name' => 'Sacatepéquez Centro', 'short' => null,],
            ['departament_id' => 4, 'name' => 'Chimaltenango Centro', 'short' => null,],
            ['departament_id' => 5, 'name' => 'Escuintla Centro', 'short' => null,],
            ['departament_id' => 6, 'name' => 'Santa Rosa Centro', 'short' => null,],
            ['departament_id' => 7, 'name' => 'Sololá Centro', 'short' => null,],
            ['departament_id' => 8, 'name' => 'Totonicapán Centro', 'short' => null,],
            ['departament_id' => 9, 'name' => 'Quetzaltenango Centro', 'short' => null,],
            ['departament_id' => 10, 'name' => 'Suchitepéquez Centro', 'short' => null,],
            ['departament_id' => 11, 'name' => 'Retalhuleu Centro', 'short' => null,],
            ['departament_id' => 12, 'name' => 'San Marcos Centro', 'short' => null,],
            ['departament_id' => 13, 'name' => 'Huehuetenango Centro', 'short' => null,],
            ['departament_id' => 14, "name" => 'Quiché Centro', "short" => null,],
            ['departament_id' => 15, 'name' => 'Baja Verapaz Centro', 'short' => null,],
            ['departament_id' => 16, 'name' => 'Alta Verapaz Centro', 'short' => null,],
            ['departament_id' => 17, 'name' => 'Petén Centro', 'short' => null,],
            ['departament_id' => 18, 'name' => 'Izabal Centro', 'short' => null,],
            ['departament_id' => 19, 'name' => 'Zacapa Centro', 'short' => null,],
            ['departament_id' => 20, 'name' => 'Chiquimula Centro', 'short' => null,],
            ['departament_id' => 21, 'name' => 'Jalapa Centro', 'short' => null,],
            ['departament_id' => 22, "name" => 'Jutiapa Centro', "short" => null,],
        ]);
    }
}

/*
 * The following data is for the agencies table.
 * The departament_id is a foreign key that references the id in the departaments table.
 * The name and short columns are used to store the name and short name of the agency.
 * The departament column is used to store the name of the department.
 */

/*
departament_id	name	short	departament
1	Guatemala Centro		Guatemala
2	El Progreso Centro		El Progreso
3	Sacatepéquez Centro		Sacatepéquez
4	Chimaltenango Centro		Chimaltenango
5	Escuintla Centro		Escuintla
6	Santa Rosa Centro		Santa Rosa
7	Sololá Centro		Sololá
8	Totonicapán Centro		Totonicapán
9	Quetzaltenango Centro		Quetzaltenango
10	Suchitepéquez Centro		Suchitepéquez
11	Retalhuleu Centro		Retalhuleu
12	San Marcos Centro		San Marcos
13	Huehuetenango Centro		Huehuetenango
14	Quiché Centro		Quiché
15	Baja Verapaz Centro		Baja Verapaz
16	Alta Verapaz Centro		Alta Verapaz
17	Petén Centro		Petén
18	Izabal Centro		Izabal
19	Zacapa Centro		Zacapa
20	Chiquimula Centro		Chiquimula
21	Jalapa Centro		Jalapa
22	Jutiapa Centro		Jutiapa
*/