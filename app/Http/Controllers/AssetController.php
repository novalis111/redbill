<?php

namespace Redbill\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Redbill\Asset;
use Redbill\AssetInterfaces\AssetInterface;
use Redbill\AssetInterfaces\RedmineInterface;
use Redbill\Company;
use Redbill\ProjectToClient;
use Redbill\Repositories\AssetRepository;
use Redbill\Repositories\CompanyRepository;

class AssetController extends Controller
{
    /**
     * @var AssetRepository
     */
    private $assets;

    /**
     * @var CompanyRepository
     */
    private $companies;

    /**
     * Create a new controller instance.
     *
     * @param AssetRepository   $assets
     * @param CompanyRepository $companies
     */
    public function __construct(AssetRepository $assets, CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->assets = $assets;
        $this->companies = $companies;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function index(Request $request)
    {
        return view('asset/index', ['groups' => $this->assets->groupedByClient()]);
    }

    public function create(Request $request, $id = false)
    {
        return view(
            'asset/edit', [
                'asset'     => new Asset(),
                'companies' => $this->companies->all(),
                'companyId' => (int)$id,
            ]
        );
    }

    public function edit(Request $request, $id)
    {
        return view(
            'asset/edit', [
                'asset'     => Asset::findOrFail($id),
                'companies' => $this->companies->all(),
                'companyId' => false,
            ]
        );
    }

    public function save(Request $request)
    {
        $this->validate(
            $request, [
                'data.client_id'     => 'required|numeric',
                'data.title'         => 'required|max:255',
                'data.amount'        => 'required|numeric',
                'data.unit'          => 'required|string|max:30',
                'data.delivery_date' => 'required|date_format:Y-m-d|before:tomorrow',
                'data.comment'       => 'sometimes|string|max:500',
            ]
        );
        /* @var Asset $asset */
        $asset = Asset::findOrNew($request->asset_id);
        if ($request->delete == 1) {
            $asset->delete();
            return redirect('asset#client' . $asset->client_id)->with('status', trans('redbill.asset_deleted'));
        }
        $asset->fill($request->data)->save();
        $msg = trans(
            'redbill.asset_name_saved', ['name' => $asset->fullTitle(), 'client' => $asset->client->company_name]
        );
        if ($request->btn_continue) {
            return redirect('asset/create/' . $asset->client_id)->with('status', $msg);
        } else {
            return redirect('asset#client' . $asset->client_id)->with('status', $msg);
        }
    }

    /**
     * @param Request $request
     * @param string  $token of interface to fetch/assign projects from
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fetchProjects(Request $request, $token)
    {
        $interface = $this->_getInterface($token);
        return view(
            'asset/fetchProjects', [
                'interfaceToken'    => $interface::TOKEN,
                'projects'          => $interface->getProjects(),
                'projectsToClients' => ProjectToClient::allForToken($interface::TOKEN),
                'companies'         => $this->companies->all(),
            ]
        );
    }

    public function bulkInsert()
    {
        return view('asset/bulkInsert', ['companies' => $this->companies->all()]);
    }

    public function bulkSave(Request $request)
    {
        $rawLines = preg_split('/\r\n|[\r\n]/', $request->bulktxt);
        $cleanLines = [];
        foreach ($rawLines as $count => $rawLine) {
            preg_match('/^([^\s]+)\s(.+)\s([\d]+)\s([\d]+)$/', $rawLine, $matches);
            if (count($matches) != 5) {
                return redirect('asset/bulkInsert')
                    ->withErrors([trans('redbill.error_in_line_nr', ['nr' => ++$count])])
                    ->withInput();
            }
            $cleanLines[] = $matches;
        }
        foreach ($cleanLines as $bulkAsset) {
            Asset::insertBulk($request->client_id, $bulkAsset[2], $bulkAsset[3], $bulkAsset[1]);
        }
        return redirect('asset')->with(
            'status', trans(
                'redbill.asset_name_saved',
                ['name' => count($rawLines), 'client' => Company::find($request->client_id)->company_name]
            )
        );
    }

    public function saveProjectsToClients(Request $request)
    {
        $projectCount = 0;
        $entryCount = 0;
        foreach ($request->projectToClient as $projectId => $clientId) {
            if ($clientId == 'none') {
                ProjectToClient::del($request->interfaceToken, $projectId);
                continue;
            }
            ProjectToClient::updateOrInsert(
                [
                    'interface_token' => $request->interfaceToken,
                    'project_id'      => $projectId,
                ], [
                    'interface_token' => $request->interfaceToken,
                    'project_id'      => $projectId,
                    'client_id'       => $clientId,
                ]
            );
            $projectCount++;
            if (isset($request->entries[$projectId])) {
                foreach ($request->entries[$projectId] as $entryId => $entryData) {
                    $assetData = [
                        'interface_token' => $request->interfaceToken,
                        'foreign_id'      => $entryId,
                        'client_id'       => $clientId,
                        'type'            => Asset::TYPE_REDMINE_TIME,
                        'title'           => $entryData['title'],
                        'amount'          => $entryData['amount'],
                        'unit'            => Asset::UNIT_HOURS,
                        'delivery_date'   => $entryData['spent_on'],
                        'comment'         => $entryData['comment'],
                    ];
                    if (!$asset = Asset::whereForeignId($entryId)
                        ->whereInterfaceToken($request->interfaceToken)->first()
                    ) {
                        /* @var Asset $asset */
                        $asset = new Asset();
                        $entryCount++;
                    }
                    $asset->fill($assetData)->save();
                }
            }
        }
        $msg = implode(
            ' ',
            [$projectCount, trans_choice('redbill.projects', $projectCount), trans('redbill.with'), $entryCount,
             trans_choice('redbill.entries', $entryCount), trans('redbill.imported')]
        );
        return redirect('asset')->with('status', $msg);
    }

    /**
     * @param string $token
     *
     * @return AssetInterface
     */
    protected function _getInterface($token)
    {
        switch (mb_strtolower($token)) {
            case RedmineInterface::TOKEN:
                $interface = new RedmineInterface();
                break;
            default:
                throw new Exception('Invalid Asset Provider: ' . $token);
                break;
        }
        return $interface;
    }
}
