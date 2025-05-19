<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DomainController extends Controller
{
    public function index()
    {
        $domains = Domain::all();
        return response()->json([
            "data" => $domains
        ]);

    }


    public function show(Domain $domain)
    {
        return response()->json([
            "data" => $domain
        ]);
    }

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

    public function destroy(Domain $domain)
    {
        $domain->delete();
        return response()->json([

        ], 200);

    }

}
