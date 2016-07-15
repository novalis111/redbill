<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;

/**
 * Redbill\ProjectToClient
 *
 * @property integer $id
 * @property string $interface_token
 * @property integer $project_id
 * @property integer $client_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Redbill\Company $client
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereInterfaceToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereProjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereClientId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\ProjectToClient whereUpdatedAt($value)
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
