<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',

            'description' => 'required|string',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'location' => 'nullable|string|max:255',

            'visibility' => 'nullable|string',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'required|exists:users,id'
        ]);

        $project = Project::create($request->all());

        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|required|string',
            'visibility' => 'nullable|string',
        ]);

        $project->update($request->all());

        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(null, 204);
    }
}
