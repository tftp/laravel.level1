<?php

namespace App\Orchid\Screens;

use App\Models\Client;
use App\Orchid\Layouts\Charts\DinamicInterviewedClients;
use App\Orchid\Layouts\Charts\PersentageFeedbackClients;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class AnalyticsAndReportsScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Аналитика и Отчеты';

    /**
     * Display header description.
     *
     * @var string|null
     */
    public $description = '';

    public $permission = ['platform.analytics', 'platform.reports'];

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'persentageFeedback' => Client::whereNotNull('assessment')->countForGroup('assessment')->toChart(),
            'interviewedClients' => [
                Client::whereNotNull('assessment')
                    ->countByDays(null, null, 'updated_at')
                    ->toChart('Опрошенные клиенты'),
                Client::countByDays()
                    ->toChart('Новые клиенты'),
            ],
        ];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    public function importClientsByPhone(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv']
        ]);

        $phones = array_map(function ($rowPhone) {
            return make_phone_normalized(array_shift($rowPhone));
        }, array_map('str_getcsv', file($request->file('file')->path())));

        $foundPhone = Client::whereIn('phone', $phones)->get();

        if ($foundPhone->count() > 0) {
            throw ValidationException::withMessages([
                'Номера телефонов, которые есть в системе ' .
                PHP_EOL .
                $foundPhone->implode('phone', ',')
            ]);
        }

        foreach ($phones as $phone) {
            Client::create([
                'name' => $phone,
            ]);
        }
        Toast::info('Новые клиенты успешно загружены');
    }

    public function exportClients()
    {
        $clients = Client::with('service')->get(['phone', 'email', 'status', 'assessment', 'service_id']);
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=clients.csv'
        ];
        $columns = ['Телефон', 'Email', 'Статус', 'Оценка', 'Услуга'];
        $callback = function () use ($clients, $columns) {
            $streem = fopen('php://output', 'w');
            fputcsv($streem, $columns);

            foreach ($clients as $client) {
                fputcsv($streem, [
                    'Телефон' => $client->phone,
                    'Email' => $client->email,
                    'Статус' => Client::STATUS[$client->status],
                    'Оценка' => $client->assessment,
                    'Услуга' => $client->service ? $client->service->name : 'null'
                ]);
            }
            fclose($streem);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::columns([
                PersentageFeedbackClients::class,
                DinamicInterviewedClients::class,
            ]),
            Layout::tabs([
                'Загрузка новых телефонов' => [
                    Layout::rows([
                        Input::make('file')
                        ->type('file')
                        ->required()
                        ->help('Необходимо загрузить файл csv с телефонами')
                        ->title('Файл с телефонами в формате csv'),
                        Button::make('Загрузить')
                        ->confirm('Вы уверены?')
                        ->type(Color::PRIMARY())
                        ->method('importClientsByPhone')
                    ])
                ],
                'Отчет по клиентам' => [
                    Layout::rows([
                        Button::make('Скачать')
                        ->method('exportClients')
                        ->rawClick() //Отключает ajax обработку
                    ])
                ]
            ])
        ];
    }
}
