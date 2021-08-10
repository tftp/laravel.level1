<?php

namespace App\Orchid\Screens\Client;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Service;
use App\Orchid\Layouts\Client\ClientListTable;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ClientListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Клиенты';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = 'Список клиентов';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'clients' => Client::filters()->defaultSort('status', 'asc')->paginate(10),
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        // Указываются ссылки и кнопки
        return [
            ModalToggle::make('Создать клиента')->modal('createClient')->method('create'),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            ClientListTable::class,
            Layout::modal('createClient', Layout::rows([
                Input::make('phone')->title('Телефон')->mask('(999) 999-99-99')->required(),
                Group::make([
                    Input::make('name')->title('Имя')->required(),
                    Input::make('last_name')->required()->title('Фамилия'),
                ]),
                Input::make('email')->required()->title('Email')->type('email'),
                DateTimer::make('birthday')->format('Y-m-d')->title('День рождения')->required(),
                Relation::make('service_id')->fromModel(Service::class, 'name')->title('Тип услуги')->required(),
            ]))->title('Создание клиента')->applyButton('Создать'),
            Layout::modal('editClient', Layout::rows([
                Input::make('client.id')->type('hidden'),
                Input::make('client.phone')->disabled()->title('Телефон'),
                Input::make('client.name')->title('Имя')->required(),
                Input::make('client.last_name')->title('Фамилия')->required(),
                Input::make('client.email')->required()->title('Email')->type('email'),
                DateTimer::make('client.birthday')->required()->title('День рождения')->format('Y-m-d'),
                Relation::make('client.service_id')->fromModel(Service::class, 'name')
                    ->title('Тип услуги')
                    ->help('Один из видов оказанных услуг для клиента')
                    ->required(),
                Select::make('client.assessment')->required()->options([
                    'Отлично' => 'Отлично',
                    'Хорошо' => 'Хорошо',
                    'Плохо' => 'Плохо',
                ])->help('Как оказана услуга')->title('Оценка')->empty('Не известно', 'Не известно'),
            ]))->title('Редактирование клиента')->applyButton('Редактировать')->async('asyncGetClient'),
        ];
    }

    public function asyncGetClient(Client $client)
    {
        return [
            'client' => $client,
        ];
    }

    public function create(ClientRequest $request)
    {

        Client::create(array_merge($request->validated(), [
            'status' => 'interviewed',
        ]));
        Toast::info('Клиент успешно создан');
    }

    public function update(Request $request)
    {
        $client = Client::find($request->input('client.id'))->update(array_merge($request->client, [
            'status' => 'interviewed',
        ]));
        Toast::info('Успешно обновлено');
    }
}

