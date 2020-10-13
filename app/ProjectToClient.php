<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;

/**
 * Redbill\ProjectToClient
 *
 * @property int $id
 * @property string $interface_token
 * @property int $project_id
 * @property int $client_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Redbill\Company $client
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereInterfaceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\ProjectToClient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProjectToClient extends Model
{
    protected $table = 'projects_to_clients';
    protected $fillable = ['interface_token', 'project_id', 'client_id'];

    public static function del($interfaceToken, $projectId)
    {
        return \DB::table('projects_to_clients')
            ->where('interface_token', '=', $interfaceToken)
            ->where('project_id', '=', $projectId)
            ->delete();
    }

    public function client()
    {
        return $this->hasOne(Company::class, 'id', 'client_id');
    }

    /**
     * @param string $token
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    static public function allForToken($token)
    {
        return self::all()->where('interface_token', $token);
    }

    static public function doesBelongTo($token, $companyId, $projectId)
    {
        return (bool)ProjectToClient::whereClientId($companyId)
            ->whereInterfaceToken($token)
            ->whereProjectId($projectId)
            ->count();
    }
}
