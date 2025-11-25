<?php

use Carbon\Carbon;

if (! function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp. ' . number_format($angka, 0, ',', '.');
    }
}

if (! function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($tanggal)
    {
        if (!$tanggal) return null;
        Carbon::setLocale('id');
        return Carbon::parse($tanggal)->translatedFormat('d F Y');
    }
}
