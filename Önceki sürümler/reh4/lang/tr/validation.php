<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'mimes' => ':attribute alanı şu türde bir dosya olmalıdır: :values.',
    'required' => ':attribute alanı gereklidir.',
    'email' => ':attribute alanı geçerli bir e-posta adresi olmalıdır.',
    'string' => ':attribute alanı bir metin olmalıdır.',
    'max' => [
        'string' => ':attribute alanı :max karakterden büyük olmamalıdır.',
        'file' => ':attribute alanı :max kilobayttan büyük olmamalıdır.',
    ],
    'min' => [
        'string' => ':attribute alanı en az :min karakter olmalıdır.',
    ],
    'unique' => ':attribute zaten alınmış.',
    'confirmed' => ':attribute onayı eşleşmiyor.',
    'regex' => ':attribute alanı formatı geçersiz.',
    'image' => ':attribute alanı bir resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'after_or_equal' => 'Son gün olarak geçmiş tarihler seçilemez',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Ad',
        'surname' => 'Soyad',
        'email' => 'E-posta',
        'password' => 'Şifre',
        'password_confirmation' => 'Şifre Tekrarı',
        'current_password' => 'Mevcut Şifre',
        'phone' => 'Telefon',
        'title' => 'Başlık',
        'role' => 'Rol',
        'department' => 'Departman',
        'photo' => 'Fotoğraf',
        'locale' => 'Dil',
        'deadline' => 'Son tarih',
    ],

];
