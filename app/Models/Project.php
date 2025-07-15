<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Project",
 *     type="object",
 *     title="Project",
 *     description="Project model",
 *     required={"title", "description", "date_start", "date_end"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the project",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the project",
 *         example="New Awesome Project"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="Slug of the project",
 *         example="new-awesome-project"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the project",
 *         example="This is a very awesome project."
 *     ),
 *     @OA\Property(
 *         property="date_start",
 *         type="string",
 *         format="date",
 *         description="Start date of the project",
 *         example="2025-07-10"
 *     ),
 *     @OA\Property(
 *         property="date_end",
 *         type="string",
 *         format="date",
 *         description="End date of the project",
 *         example="2025-12-31"
 *     ),
 *     @OA\Property(
 *         property="budget",
 *         type="number",
 *         format="float",
 *         description="Budget of the project",
 *         example=5000.50
 *     ),
 *     @OA\Property(
 *         property="location",
 *         type="string",
 *         description="Location of the project",
 *         example="New York, USA"
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="string",
 *         description="Visibility of the project",
 *         enum={"public", "private"},
 *         example="public"
 *     ),
 *     @OA\Property(
 *         property="status_id",
 *         type="integer",
 *         description="Status ID of the project",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="integer",
 *         description="User ID of the creator",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="User ID of the last updater",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation date",
 *         example="2025-07-10T17:32:41.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update date",
 *         example="2025-07-10T17:32:41.000000Z"
 *     )
 * )
 */
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [

        'title',
        'slug',
        'description',
        'date_start',
        'date_end',
        'budget',
        'location',
        'visibility',
        'created_by',
        'updated_by',
        'status_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
   public function roles()
{
    return $this->belongsToMany(Role::class, 'project_role')
                ->withPivot('description');

}

    public function domains(){
        return $this->belongsToMany(Domain::class);
    }
    public function projectRoles()
{
    return $this->hasMany(ProjectRole::class);
}
public function status()
{
    return $this->belongsTo(Status::class);
}

}
