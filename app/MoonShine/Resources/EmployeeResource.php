<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Request;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\Title;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\LineBreak;
use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\File;
use MoonShine\Fields\Image;
use MoonShine\Fields\Number;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Td;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;

/**
 * @extends ModelResource<Employee>
 */
class EmployeeResource extends ModelResource
{
    protected string $model = Employee::class;

    protected string $title = 'Employees';

    protected bool $createInModal = false; 
    protected bool $editInModal = true;  
    protected bool $detailInModal = true;

    protected bool $withPolicy = false;

    protected int $itemsPerPage = 10;

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                Title::make('Datos del Empleado'),
                LineBreak::make(),
                Grid::make([
                    Column::make([
                        ID::make()->sortable(),
                        Text::make('Nombres', 'name')
                            ->customAttributes(['class' => 'AA']) 
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        Text::make('Apellidos', 'last_name')
                        ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Género', 'genders', 'name')
                        ->required()
                        ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        Date::make('Fecha de nacimiento', 'birth_date')
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Estado Civil', 'civilStatus', 'name')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        Number::make('Edad', 'age')
                            ->required(),
                    ])->columnSpan(2),
                ]),
                LineBreak::make(),
                Grid::make([
                    Column::make([
                        Text::make('Dirección Domiciliaria', 'address')
                            ->required(),
                    ])->columnSpan(3),
                    Column::make([
                        BelongsTo::make('Municipio', 'towns', 'name')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Departamento', 'departaments', 'name')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        Text::make('Zona', 'zone')
                            ->required(),
                    ])->columnSpan(1),
                    Column::make([
                        Text::make('Lugar de Nacimiento', 'birthplace')
                            ->required(),
                    ])->columnSpan(3),
                    Column::make([
                        Number::make('Hijos', 'children')
                            ->required(),
                    ])->columnSpan(1),
                ]),
                LineBreak::make(),
                Grid::make([
                    Column::make([
                        Text::make('Nacionalidad', 'nationality')
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        Phone::make('Teléfono', 'phone')
                            ->required(),
                    ])->columnSpan(1),
                    Column::make([
                        Phone::make('Celular', 'cellphone')
                            ->required(),
                    ])->columnSpan(1),
                    Column::make([
                        Number::make('DPI', 'dpi')
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        Number::make('NIT', 'nit')
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        Email::make('Correo Electrónico', 'email')
                            ->required(),
                    ])->columnSpan(4),
                ]),
                LineBreak::make(),
                Divider::make(),
                Title::make('Datos de Contratación'),
                LineBreak::make(),
                Grid::make([
                    Column::make([      
                        Date::make('Fecha Ingreso', 'entry_date')
                            ->format('d/m/Y') 
                            ->required(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Agencia', 'agencies', 'short')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Cargo', 'charges', 'name')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(2),
                    Column::make([
                        BelongsTo::make('Estado Planilla', 'payrolls', 'state')
                            ->required()
                            ->searchable(),
                    ])->columnSpan(3),
                    Column::make([
                        Number::make('Cuenta Bancaria', 'bank_account')
                            ->required(),
                    ])->columnSpan(3),
                ]),
                LineBreak::make(),
                Grid::make([
                    Column::make([
                        Image::make('Fotografía', 'photo')
                            ->allowedExtensions(['png', 'jpg', 'jpge'])
                            ->disk('public')
                            ->required(),
                    ])->columnSpan(6),
                    Column::make([
                        Textarea::make('Observaciones', 'comments'),
                    ])->columnSpan(6)
                ]),

            ])
        ];
    }

    /**
     * @param Employee $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}