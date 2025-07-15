<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/domains",
     *     summary="Get a list of domains",
     *     tags={"Domains"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Domain")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $domains = Domain::all();
        return response()->json([
            "data" => $domains
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/domains/{domain}",
     *     summary="Get a specific domain",
     *     tags={"Domains"},
     *     @OA\Parameter(
     *         name="domain",
     *         in="path",
     *         required=true,
     *         description="ID of the domain",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Domain")
     *     )
     * )
     */
    public function show(Domain $domain)
    {
        return response()->json([
            "data" => $domain
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/domains",
     *     summary="Create a new domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Web Development")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Domain")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $domain = Domain::create($request->all());

        return response()->json([
            "data" => $domain
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/domains/{domain}",
     *     summary="Update a domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="domain",
     *         in="path",
     *         required=true,
     *         description="ID of the domain",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Web Development")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Domain")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, Domain $domain)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $domain->update($request->all());

        return response()->json([
            "data" => $domain
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/domains/{domain}",
     *     summary="Delete a domain",
     *     tags={"Domains"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="domain",
     *         in="path",
     *         required=true,
     *         description="ID of the domain",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Domain deleted successfully"
     *     )
     * )
     */
    public function destroy(Domain $domain)
    {
        $domain->delete();
        return response()->json([], 200);
    }
}
