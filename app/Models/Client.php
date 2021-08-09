<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Propaganistas\LaravelPhone\PhoneNumber;

class Client extends Model
{
    use HasFactory;
    use AsSource;
    use Filterable; // расширит модель дополнительными методами

    protected $fillable = ['phone', 'name', 'last_name', 'status', 'email', 'birthday', 'service_id', 'assessment'];

    protected $allowedSorts = [
        'status',
    ]; // свойство с доступными колонками

    protected $allowedFilters = [
        'phone',
    ];

    public function setPhoneAttribute($phone)
    {
        $this->attributes['phone'] = str_replace('+', '', PhoneNumber::make($phone, 'RU')->formatE164());
    }
}
