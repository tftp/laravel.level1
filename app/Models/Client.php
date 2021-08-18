<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Metrics\Chartable;
use Orchid\Screen\AsSource;
use Propaganistas\LaravelPhone\PhoneNumber;

class Client extends Model
{
    use HasFactory;
    use Chartable;
    use AsSource;
    use Filterable; // расширит модель дополнительными методами

    protected $fillable = ['phone', 'name', 'last_name', 'status', 'email', 'birthday', 'service_id', 'assessment'];

    protected $allowedSorts = [
        'status',
    ]; // свойство с доступными колонками

    protected $allowedFilters = [
        'phone',
    ];

    public const STATUS = [
        'interviewed' => 'Опрошен',
        'not_interviewed' => 'Не опрошен',
    ];

    public function setPhoneAttribute($phone)
    {
        $this->attributes['phone'] = make_phone_normalized($phone);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
