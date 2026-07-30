<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PelatihanController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\SertifikatController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KategoriPelatihanController;
use App\Http\Controllers\Admin\SertifikatDesignController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboard;
use App\Http\Controllers\Mentor\PresensiController;
use App\Http\Controllers\Mentor\RekapController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboard;
use App\Http\Controllers\Peserta\PelatihanController as PesertaPelatihanController;
use App\Http\Controllers\Peserta\AktivitasController;
use App\Http\Controllers\Peserta\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

//Redirect ke dashboard sesuai role
Route::get('/dashboard', [RedirectController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

//ADMIN
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    //Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    //Pelatihan CRUD
    Route::get('/pelatihan',               [PelatihanController::class, 'index'])->name('pelatihan.index');
    Route::get('/pelatihan/create',        [PelatihanController::class, 'create'])->name('pelatihan.create');
    Route::post('/pelatihan',              [PelatihanController::class, 'store'])->name('pelatihan.store');
    Route::get('/pelatihan/{id}',          [PelatihanController::class, 'show'])->name('pelatihan.show');
    Route::get('/pelatihan/{id}/edit',     [PelatihanController::class, 'edit'])->name('pelatihan.edit');
    Route::put('/pelatihan/{id}',          [PelatihanController::class, 'update'])->name('pelatihan.update');
    Route::delete('/pelatihan/{id}',       [PelatihanController::class, 'destroy'])->name('pelatihan.destroy');
    Route::patch('/pelatihan/{id}/status', [PelatihanController::class, 'updateStatus'])->name('pelatihan.status');

    //Kategori Pelatihan 
    Route::get('/kategori',                [KategoriPelatihanController::class, 'index'])->name('kategori.index');
    Route::post('/kategori',               [KategoriPelatihanController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}',           [KategoriPelatihanController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}',        [KategoriPelatihanController::class, 'destroy'])->name('kategori.destroy');
    Route::post('/kategori/ajax',          [KategoriPelatihanController::class, 'storeAjax'])->name('kategori.storeAjax');

    //Pendaftaran
    Route::get('/pendaftaran',                       [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('/pendaftaran/{id}/verifikasi',      [PendaftaranController::class, 'verifikasi'])->name('pendaftaran.verifikasi');
    Route::post('/pendaftaran/{id}/tolak',           [PendaftaranController::class, 'tolak'])->name('pendaftaran.tolak');
    Route::post('/pendaftaran/{id}/batal-verifikasi', [PendaftaranController::class, 'batalVerifikasi'])->name('pendaftaran.batal-verifikasi');
    Route::get('/pendaftaran/{id}/bukti',            [PendaftaranController::class, 'lihatBukti'])->name('pendaftaran.bukti');

    //Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    //Sertifikat & Laporan (statis sebelum {id})
    Route::get('/sertifikat',              [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::get('/sertifikat/laporan-pdf',  [SertifikatController::class, 'laporanPdf'])->name('sertifikat.laporan-pdf');
    Route::post('/sertifikat/{id}/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
    Route::get('/sertifikat/{id}/download',  [SertifikatController::class, 'download'])->name('sertifikat.download');

    //Kelola Desain Sertifikat
    Route::get('/desain-sertifikat',           [SertifikatDesignController::class, 'index'])->name('desain-sertifikat.index');
    Route::get('/desain-sertifikat/{id}/edit',  [SertifikatDesignController::class, 'edit'])->name('desain-sertifikat.edit');
    Route::put('/desain-sertifikat/{id}',       [SertifikatDesignController::class, 'update'])->name('desain-sertifikat.update');

    //Manajemen User
    Route::get('/users',                          [UserController::class, 'index'])->name('users.index');
    Route::post('/users/mentor',                  [UserController::class, 'storeMentor'])->name('users.mentor.store');
    Route::post('/users/mentor/assign',           [UserController::class, 'assignMentor'])->name('users.mentor.assign');
    Route::post('/users/mentor/unassign',         [UserController::class, 'unassignMentor'])->name('users.mentor.unassign');
    Route::delete('/users/mentor/{id}',           [UserController::class, 'destroyMentor'])->name('users.mentor.destroy');
    Route::post('/users/peserta/{id}/reset-password', [UserController::class, 'resetPasswordPeserta'])->name('users.peserta.reset-password');

    //Profil Admin
    Route::get('/profil',          [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil',        [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::delete('/profil',       [ProfileController::class, 'destroy'])->name('profil.destroy');

    Route::get('/nota/{id}/download', [\App\Http\Controllers\Peserta\NotaController::class, 'download'])->name('nota.download');
    //Legacy redirects
    Route::get('/lokasi',    fn() => redirect()->route('admin.dashboard'))->name('lokasi.index');
    Route::get('/pendaftar', fn() => redirect()->route('admin.pendaftaran.index'))->name('pendaftar.index');
});

//MENTOR
Route::prefix('mentor')->middleware(['auth', 'role:mentor'])->name('mentor.')->group(function () {
    Route::get('/dashboard',        [MentorDashboard::class, 'index'])->name('dashboard');
    Route::get('/presensi',         [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi/simpan', [PresensiController::class, 'simpan'])->name('presensi.simpan');
    Route::get('/rekap',            [RekapController::class, 'index'])->name('rekap.index');
    //Profil
    Route::get('/profil',          [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil',        [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::delete('/profil',       [ProfileController::class, 'destroy'])->name('profil.destroy');
});

//PESERTA
Route::prefix('peserta')->middleware(['auth', 'role:peserta'])->name('peserta.')->group(function () {
    Route::get('/dashboard',                        [PesertaDashboard::class, 'index'])->name('dashboard');
    Route::get('/pelatihan',                        [PesertaPelatihanController::class, 'index'])->name('pelatihan.index');
    Route::post('/pelatihan/daftar',                [PesertaPelatihanController::class, 'daftar'])->name('pelatihan.daftar');
    Route::post('/pelatihan/bukti/{id}',            [PesertaPelatihanController::class, 'uploadBukti'])->name('pelatihan.upload-bukti');
    Route::get('/aktivitas',                        [AktivitasController::class, 'index'])->name('aktivitas.index');
    Route::post('/aktivitas/{id}/upload-bukti',     [AktivitasController::class, 'uploadBukti'])->name('aktivitas.upload-bukti');
    Route::get('/riwayat',                          [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/sertifikat/{id}/download', [RiwayatController::class, 'downloadSertifikat'])->name('riwayat.sertifikat.download');
    Route::get('/nota/{id}/download',               [\App\Http\Controllers\Peserta\NotaController::class, 'download'])->name('nota.download');
    //Profil
    Route::get('/profil',          [ProfileController::class, 'edit'])->name('profil.edit');
    Route::patch('/profil',        [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::delete('/profil',       [ProfileController::class, 'destroy'])->name('profil.destroy');
});

//PROFILE (shared - legacy)
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
