<?php

namespace App\Orchid\Layouts;

use App\Models\Service;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class CreateOrUpdateClient extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): array
    {
        $isClientExist = is_null($this->query->getContent('client')) === false;

        return [
            Input::make('client.id')->type('hidden'),
            Input::make('client.phone')->title('Телефон')->mask('(999) 999-99-99')->required()->disabled($isClientExist),
            Group::make([
                Input::make('client.name')->title('Имя')->required()->placeholder('Имя клиента'),
                Input::make('client.last_name')->required()->title('Фамилия')->placeholder('Фамилия Клиента'),
            ]),
            Input::make('client.email')->required()->title('Email')->type('email'),
            DateTimer::make('client.birthday')->format('Y-m-d')->title('День рождения')->required(),
            Relation::make('client.service_id')->fromModel(Service::class, 'name')->title('Тип услуги')->required()
            ->help('Один из видов оказания услуг'),
            Select::make('client.assessment')->required()->options([
                'Отлично' => 'Отлично',
                'Хорошо' => 'Хорошо',
                'Плохо' => 'Плохо',
            ])->help('Как оказана услуга')->title('Оценка')->empty('Не известно', 'Не известно'),
        ];
    }
}
