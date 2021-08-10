<?php

namespace App\Orchid\Screens\Client;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Orchid\Layouts\Client\ClientListTable;
use App\Orchid\Layouts\CreateOrUpdateClient;
use Orchid\Screen\Actions\ModalToggle;
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
            ModalToggle::make('Создать клиента')->modal('createClient')->method('createOrUpdate'),
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
            Layout::modal('createClient', CreateOrUpdateClient::class)
                ->title('Создание клиента')
                ->applyButton('Создать'),
            Layout::modal('editClient', CreateOrUpdateClient::class)
                ->title('Редактирование клиента')
                ->applyButton('Редактировать')
                ->async('asyncGetClient'),
        ];
    }

    public function asyncGetClient(Client $client)
    {
        return [
            'client' => $client,
        ];
    }

    public function createOrUpdate(ClientRequest $request)
    {
        $clientID = $request->input('client.id');
        Client::updateOrCreate([
            'id' => $clientID
        ], array_merge($request->validated()['client'], [
            'status' => 'interviewed'
        ]));

        is_null($clientID) ? Toast::info('Успешно создано') : Toast::info('Успешно изменено');
    }
}

