<?php

if (! function_exists('make_phone_normalized')) {
    function make_phone_normalized(string $rowPhone):string {
        return str_replace('+', '', \Propaganistas\LaravelPhone\PhoneNumber::make($rowPhone, 'RU')->formatE164());
    }
}
