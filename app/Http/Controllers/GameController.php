<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $validSortBy = ['title', 'popular', 'uploaddate'];
        $sortBy = in_array($sortBy, $validSortBy) ? $sortBy : 'title';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

        $query = Game::query();

        if ($sortBy === 'popular') {
            $query->withCount('scores as scoreCount')->orderBy('scoreCount', $sortDir);
        } elseif ($sortBy === 'uploaddate') {
            $query->orderBy('upload_timestamp', $sortDir);
        } else {
            $query->orderBy('title', $sortDir);
        }

        $totalElements = $query->count();
        $games = $query->skip($page * $size)->take($size)->get();

        $content = $games->map(function ($game) {
            return [
                'slug' => $game->slug,
                'title' => $game->title,
                'description' => $game->description,
                'thumbnail' => $game->latestVersion ? $game->latestVersion->thumbnail : null,
                'uploadTimestamp' => $game->latestVersion ? $game->latestVersion->upload_timestamp : null,
                'author' => $game->author?->name,
                'scoreCount' => $game->scores()->count()
            ];
        });

        $pageCount = ceil($totalElements / $size);
        $isLastPage = ($page + 1) * $size >= $totalElements;

        return response()->json([
            'page' => $page,
            'size' => $size,
            'totalElements' => $totalElements,
            'content' => $content,
            'pageCount' => $pageCount,
            'isLastPage' => $isLastPage
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:60',
            'description' => 'required|max:200',
        ]);

        $slug = Str::slug($request->title);
        if (Game::where('slug', $slug)->exists()) {
            return response()->json([
                'status' => 'invalid',
                'slug' => 'Game title already exists'
            ], 400);
        }

   
        $username = $request->user()->username; 
        if (strpos($username, 'dev') === false) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Only users with "dev" in their username can create games.'
            ], 403); 
        }

        $game = new Game;
        $game->title = $request->title;
        $game->description = $request->description;
        $game->slug = $slug;
        $game->created_by = $request->user()->id;
        $game->save();

        return response()->json([
            'status' => 'success',
            'slug' => $slug
        ], 201);
    }

    public function show($slug)
    {
        $game = Game::where('slug', $slug)->with('latestVersion')->first();

        if (!$game) {
            return response()->json(null, 404);
        }

        return response()->json([
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => $game->latestVersion ? $game->latestVersion->thumbnail : null,
            'uploadTimestamp' => $game->latestVersion ? $game->latestVersion->upload_timestamp : null,
            'author' => $game->author->name,
            'scoreCount' => $game->scores()->count(),
            'gamePath' => "/games/{$slug}/{$game->latestVersion->id}/"
        ], 200);
    }
}