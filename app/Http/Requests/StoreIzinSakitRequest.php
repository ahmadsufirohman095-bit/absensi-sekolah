<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreIzinSakitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            // PERUBAHAN: Validasi 'nis' diubah menjadi 'user_id'
            'user_id' => [
                'required',
                'integer',
                'exists:users,id,role,siswa', // Pastikan ID ada di tabel users dengan role siswa
            ],
            'tanggal' => 'required|date|before_or_equal:today',
            'status' => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string|max:255',
            'bukti_absensi' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // PERUBAHAN: Pesan error disesuaikan untuk 'user_id'
            'user_id.required' => 'Siswa wajib dipilih.',
            'user_id.exists' => 'Siswa yang dipilih tidak valid atau bukan seorang siswa.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'bukti_absensi.mimes' => 'Format file bukti harus berupa JPG, JPEG, PNG, atau PDF.',
            'bukti_absensi.max' => 'Ukuran file bukti tidak boleh lebih dari 2MB.',
        ];
    }
}