<?php

namespace App\Orchid\Screens\Client;

use App\Models\Client;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

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
            'clients' => Client::filters()->defaultSort('status', 'desc')->paginate(10),
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
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('clients', [
                TD::make('phone', 'Телефон')->width('150px')->cantHide()->canSee($this->isWorkTime())->filter(TD::FILTER_TEXT),
                TD::make('status', 'Статус')->render(function (Client $client) {
                    return $client->status === 'interviewed' ? 'Опрошен' : 'Не опрошен';
                })->width('150px')->popover('Статус по результатам работы оператора')->sort(),
                TD::make('email', 'Email'),
                TD::make('assessment', 'Оценка')->width('200px')->align(TD::ALIGN_RIGHT),
                TD::make('created_at', 'Дата создания')->defaultHidden(),
                TD::make('updated_at', 'Дата обновления')->defaultHidden(),
            ])
        ];
    }

    // Пример использования для метода -> canSee, который зависит от логического значения
    private function isWorkTime():bool
    {
        $lunch = CarbonPeriod::create('13:00', '14:00');
        return $lunch->contains(Carbon::now(config('app.timezone'))) === false;
    }
}
