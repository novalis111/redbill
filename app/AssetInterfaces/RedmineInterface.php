<?php

namespace Redbill\AssetInterfaces;

use Illuminate\Database\MySqlConnection;
use Redbill\Asset;

class RedmineInterface extends AssetInterface
{
    const TOKEN = 'redmine';

    /**
     * @var MySqlConnection
     */
    private $connection;

    public function getProjects()
    {
        // Fetch projects - http://www.klempert.de/nested_sets/
        $projects = [];
        $rows = $this->connection->query()->selectRaw(
            "
            n.name, n.id,
            COUNT(*)-1 AS level
            FROM projects AS n, projects AS p
            WHERE n.status = 1
            AND n.description NOT LIKE '%#nobill#%'
            AND n.lft BETWEEN p.lft AND p.rgt
            GROUP BY n.lft
            ORDER BY n.lft, n.name
            "
        )->get();
        foreach ($rows as $row) {
            /* @var \stdClass $row */
            $projects[] = [
                'project' => $row,
                'entries' => $this->_getProjectEntries($row->id)
            ];
        }
        return $projects;
    }

    protected function _connect()
    {
        $this->connection = \DB::connection(self::TOKEN);
    }

    /**
     * @param int $projectId foreign project id
     *
     * @return array
     */
    protected function _getProjectEntries($projectId)
    {
        $result = [];
        /* @var \Illuminate\Database\Query\Builder $builder */
        $rows = $this->connection->query()
            ->selectRaw(
                'te.id as entryId, te.spent_on, te.hours AS amount, iu.subject AS title, te.project_id, te.comments as comment'
            )
            ->from('time_entries AS te')
            ->join('issues AS iu', 'te.issue_id', '=', 'iu.id')
            ->where('te.project_id', '=', $projectId)
            ->orderBy('spent_on', 'desc')
            ->get();
        if (count($rows) > 0) {
            // Filter out rows which already have a matching entry in redbill
            foreach ($rows as $row) {
                /* @var \stdClass $row */
                if (Asset::whereForeignId($row->entryId)->where('amount', '<=', $row->amount)->count() == 0) {
                    $result[] = $row;
                }
            }
        }
        return $result;
    }
}