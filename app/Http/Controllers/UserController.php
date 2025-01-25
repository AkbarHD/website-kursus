<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kursus;
use App\Models\Pemberitahuan;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $email = auth()->user()->email;
        $thisKursus = Kursus::where('email', $email)->first();
        $kursus = $thisKursus->pluck('kursus')->implode(', ');
        $totalDisetujui = $thisKursus->where('status', 'disetujui')->count();
        $pemberitahuan = Pemberitahuan::where('email', $email)->get();
        $totalPemberitahuan = $pemberitahuan->count();
        $berita = Berita::first();
        return view('user.index', [
            'kursus' => $kursus,
            'totalDisetujui' => $totalDisetujui,
            'totalPemberitahuan' => $totalPemberitahuan,
            'pemberitahuan' => $pemberitahuan,
            'berita' => $berita
        ]);
    }
}
