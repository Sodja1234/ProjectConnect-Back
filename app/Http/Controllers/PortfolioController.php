<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\PortfolioResource;
use Database\Seeders\PortfolioSeeder;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Portfolio::with(['skills']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $portfolios = $query->get();

        return PortfolioResource::collection($portfolios);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'skill' => 'array',
            'skill.*' => 'exists:skills,id',
        ]);

        $user = auth()->user();

        $portfolio = Portfolio::create(array_merge($request->all(), [
            'user_id' => $user->id
        ]));

        if ($request->has('skill')) {
            $portfolio->skills()->attach($request->skill);
        }

        return (new PortfolioResource($portfolio->load('skills', 'user')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        try {
            $portfolio = Portfolio::with(['skills', 'user'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Portfolio non trouvé'], 404);
        }

        if ($user->id !== $portfolio->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return new PortfolioResource($portfolio);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $portfolio = Portfolio::findOrFail($id);
        $portfolio->skills()->detach();
        $portfolio->delete();

        return response()->json(['message' => 'Portfolio supprimé avec succès.']);
    }

public function myPortfolio()
{
    $user = auth()->user();

    // Charge les relations sur les portfolios
    $user->load('portfolios.skills', 'portfolios.user');

    // Retourne une collection formatée grâce à PortfolioResource
    return PortfolioResource::collection($user->portfolios);
}

}
