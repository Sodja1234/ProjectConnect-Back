<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\PortfolioResource;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/portfolios",
     *     summary="Get list of portfolios",
     *     description="Retrieve a list of portfolios, optionally filtered by user_id",
     *     tags={"Portfolios"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter portfolios by user ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with list of portfolios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Portfolio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/portfolios",
     *     summary="Create a new portfolio",
     *     tags={"Portfolios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Portfolio")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Portfolio created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Portfolio")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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

        $user = Auth::user();

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
     * @OA\Get(
     *     path="/api/portfolios/{id}",
     *     summary="Get a specific portfolio",
     *     tags={"Portfolios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the portfolio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Portfolio")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Portfolio not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = Auth::user();

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
     * @OA\Delete(
     *     path="/api/portfolios/{id}",
     *     summary="Delete a portfolio",
     *     tags={"Portfolios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the portfolio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Portfolio deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Portfolio not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $portfolio = Portfolio::findOrFail($id);
        $portfolio->skills()->detach();
        $portfolio->delete();

        return response()->json(['message' => 'Portfolio supprimé avec succès.']);
    }

    /**
     * @OA\Get(
     *     path="/api/myPortfolio",
     *     summary="Get the current user's portfolio",
     *     tags={"Portfolios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Portfolio")
     *         )
     *     )
     * )
     */
    public function myPortfolio()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Charge les relations sur les portfolios
        $user->load('portfolios.skills', 'portfolios.user');

        // Retourne une collection formatée grâce à PortfolioResource
        return PortfolioResource::collection($user->portfolios);
    }
}
