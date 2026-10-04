<?php
namespace App\Http\Controllers\Home;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('home.landing-page.index');
    }

    public function getData(Request $request)
    {
        $data = User::civitasOnly()
            ->where('departemen_id', $request->departemen_id)
            ->limit(8)
            ->get();
        return view('home.landing-page.data', compact('data'));
    }

    public function searchCivitas(Request $request)
    {
        $departemenId = $request->get('departemen_id');
        $queryText = trim((string)$request->get('q', ''));

        $query = User::civitasOnly()
            ->with('departemen');

        if ($departemenId && $departemenId !== '*' && $departemenId !== 'all') {
            $query->where('departemen_id', $departemenId);
        }

        if ($queryText !== '') {
            $query->where(function ($sub) use ($queryText) {
                $sub->where('name', 'like', "%{$queryText}%")
                    ->orWhere('id', 'like', "%{$queryText}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->limit(30)->get();

        return response()->json([
            'status' => 'success',
            'total' => $users->count(),
            'query' => $queryText,
            'departemen_id' => $departemenId,
            'data' => $users->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'departemen_id' => $u->departemen_id,
                    'departemen_nama' => $u->departemen ? $u->departemen->nama : 'Umum',
                    'photo' => $u->photo ? asset('photo/' . $u->photo) : asset('home/assets/imgs/theme/user.png'),
                    'url' => route('absensi.show', ['user' => $u->id]),
                ];
            })
        ]);
    }
}
