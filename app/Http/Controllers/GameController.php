<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $query = Game::query();

        // Menambahkan logika untuk menghitung scoreCount
        $query->withCount('scores as scoreCount');

        // Menyertakan data terbaru dari game version untuk thumbnail dan uploadTimestamp
        $query->with(['versions' => function ($query) {
            $query->latest('upload_timestamp');
        }]);

        // Menyaring game yang memiliki setidaknya satu versi
        $query->has('versions');

        // Mengurutkan hasil berdasarkan parameter yang diberikan
        if ($sortBy === 'popular') {
            $query->orderBy('scoreCount', $sortDir);
        } elseif ($sortBy === 'uploaddate') {
            $query->orderBy('versions.upload_timestamp', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $games = $query->paginate($size, ['*'], 'page', $page);

        // Menyiapkan data untuk response
        $response = [
            'page' => $games->currentPage(),
            'size' => $games->perPage(),
            'totalElements' => $games->total(),
            'content' => $games->items()->map(function ($game) {
                return [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description,
                    'thumbnail' => $game->versions->first()->thumbnail ?? null,
                    'uploadTimestamp' => $game->versions->first()->upload_timestamp,
                    'author' => $game->author,
                    'scoreCount' => $game->scoreCount
                ];
            }),
        ];

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
