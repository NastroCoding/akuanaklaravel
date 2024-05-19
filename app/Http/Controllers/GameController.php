<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GameController extends Controller
{

    public function index(Request $request)
    {

        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $page = max(0, intval($page));
        $size = max(1, intval($size));
        $sortBy = in_array($sortBy, ['title', 'popular', 'uploaddate']) ? $sortBy : 'title';
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';

        $query = Game::query();

        $query->orderBy($sortBy, $sortDir);

        $totalElements = $query->count();
        $content = $query->skip($page * $size)->take($size)->get();

        $response = [
            'page' => $page,
            'size' => $size,
            'totalElements' => $totalElements,
            'content' => $content
        ];

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:3|max:60',
            'description' => 'required|max:200',
        ]);

        $slug = Str::slug($validated['title']);
        if (Game::where('slug', $slug)->exists()) {
            return response()->json(['status' => 'invalid', 'slug' => 'Game title already exists'], 400);
        }

        $userId = Auth::id();
        if (is_null($userId)) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $game = Game::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'slug' => $slug,
            'created_by' => $userId,
        ]);

        return response()->json(['status' => 'success', 'slug' => $slug], 201);
    }

    // Method untuk menghasilkan slug yang unik
    private function generateUniqueSlug($title)
    {
        // Menghasilkan slug dari judul
        $slug = strtolower(str_replace(' ', '-', $title));

        // Memastikan slug unik dengan menambahkan angka acak jika diperlukan
        $existingGame = Game::where('slug', $slug)->first();
        $counter = 1;
        while ($existingGame) {
            $slug = $slug . '-' . $counter;
            $existingGame = Game::where('slug', $slug)->first();
            $counter++;
        }

        return $slug;
    }
    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $game = Game::with(['versions' => function ($query) {
            $query->latest('created_at');
        }])->where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $latestVersion = $game->versions->first();
        $response = [
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => $latestVersion ? "/games/{$game->slug}/{$latestVersion->id}/thumbnail.png" : null,
            'uploadTimestamp' => $latestVersion ? $latestVersion->created_at->toIso8601String() : null,
            'author' => $game->created_by,
            'scoreCount' => $game->versions->sum('scores_count'), // Pastikan Anda memiliki relasi dan penghitungan yang benar
            'gamePath' => $latestVersion ? "/games/{$game->slug}/{$latestVersion->id}/" : null,
        ];

        return response()->json($response, 200);
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
