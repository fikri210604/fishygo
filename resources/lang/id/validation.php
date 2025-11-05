<?php

return [
    'required' => ':attribute wajib diisi.',
    'unique' => ':attribute sudah terdaftar.',
    'email' => 'Format :attribute tidak valid.',
    'image' => ':attribute harus berupa gambar.',
    'max' => [
        'file' => ':attribute maksimal :max KB.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',

    'attributes' => [
        'nama' => 'Nama',
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'avatar' => 'Avatar',
        'province_id' => 'Provinsi',
        'regency_id' => 'Kabupaten/Kota',
        'district_id' => 'Kecamatan',
        'village_id' => 'Kelurahan/Desa',
    ],
];

