<?php
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable','image','max:2048'],
            'province_id' => ['nullable','string','max:50'],
            'province_name' => ['nullable','string','max:100'],
            'regency_id' => ['nullable','string','max:50'],
            'regency_name' => ['nullable','string','max:100'],
            'district_id' => ['nullable','string','max:50'],
            'district_name' => ['nullable','string','max:100'],
            'village_id' => ['nullable','string','max:50'],
            'village_name' => ['nullable','string','max:100'],
            'subdistrict_id' => ['nullable','string','max:50'],
            'subdistrict_name' => ['nullable','string','max:100'],
            'rt' => ['nullable','string','max:10'],
            'rw' => ['nullable','string','max:10'],
            'kode_pos' => ['nullable','string','max:10'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'address.max' => 'Alamat maksimal :max karakter.',
            'phone.max' => 'Nomor HP maksimal :max karakter.',
            'avatar.image' => 'Avatar harus berupa gambar.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama',
            'username' => 'Username',
            'email' => 'Email',
            'avatar' => 'Avatar',
            'address' => 'Alamat',
            'phone' => 'Nomor HP',
            'province_id' => 'Provinsi',
            'regency_id' => 'Kabupaten/Kota',
            'district_id' => 'Kecamatan',
            'village_id' => 'Kelurahan/Desa',
            'rt' => 'RT',
            'rw' => 'RW',
            'kode_pos' => 'Kode Pos',
        ];
    }
}

