<?php

namespace Redbill\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Redbill\Company;
use Redbill\Http\Requests;
use Redbill\Invoice;
use Redbill\InvoiceEntry;
use Redbill\Repositories\CompanyRepository;
use Redbill\Repositories\InvoiceRepository;

class InvoiceController extends Controller
{
    /**
     * @var InvoiceRepository
     */
    private $invoices;

    /**
     * @var CompanyRepository
     */
    private $companies;

    /**
     * Create a new controller instance.
     *
     * @param InvoiceRepository $invoices
     * @param CompanyRepository $companies
     */
    public function __construct(InvoiceRepository $invoices, CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->invoices = $invoices;
        $this->companies = $companies;
    }

    /**
     * @param Request     $request
     *
     * @param string|bool $from date from (eg 2016-02-29)
     * @param string|bool $to   date to (eg 2016-02-29)
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $from = false, $to = false)
    {
        if ($from) {
            \Session::set('invoiceFrom', $from);
        }
        if ($to) {
            \Session::set('invoiceTo', $to);
        }
        $invoices['open'] = $this->invoices->open();
        $invoices['payed'] = $this->invoices->payed();
        $invoices['cancelled'] = $this->invoices->cancelled();
        $sums = [
            'open'      => ['gross' => 0, 'net' => 0, 'profit' => 0],
            'payed'     => ['gross' => 0, 'net' => 0, 'profit' => 0],
            'cancelled' => ['gross' => 0, 'net' => 0, 'profit' => 0],
        ];
        foreach ($invoices['open'] as $openInv) {
            /* @var \Redbill\Invoice $openInv */
            $sums['open']['gross'] += $openInv->getGrossSum();
            $sums['open']['net'] += $openInv->getNetSum();
            $sums['open']['profit'] += $openInv->getProfitSum();
        }
        foreach ($invoices['payed'] as $payedInv) {
            /* @var \Redbill\Invoice $payedInv */
            $sums['payed']['gross'] += $payedInv->getGrossSum();
            $sums['payed']['net'] += $payedInv->getNetSum();
            $sums['payed']['profit'] += $payedInv->getProfitSum();
        }
        foreach ($invoices['cancelled'] as $cancelledInv) {
            /* @var \Redbill\Invoice $cancelledInv */
            $sums['cancelled']['gross'] += $cancelledInv->getGrossSum();
            $sums['cancelled']['net'] += $cancelledInv->getNetSum();
            $sums['cancelled']['profit'] += $cancelledInv->getProfitSum();
        }
        $spanOptions = $this->_getTimeSpans();
        return view('invoice/index', compact('invoices', 'sums', 'spanOptions'));
    }

    public function create(Request $request, $id = false)
    {
        $invoice = new Invoice();
        if (Company::find($id)) {
            $invoice->setAttribute('client_id', $id);
        }
        return view(
            'invoice/edit', [
                'invoice'   => $invoice,
                'companies' => $this->companies->all(),
            ]
        );
    }

    public function edit(Request $request, $id)
    {
        return view(
            'invoice/edit', [
                'invoice'   => Invoice::findOrFail($id),
                'companies' => $this->companies->all(),
                'clientId'  => false,
            ]
        );
    }

    public function save(Requests\SaveInvoiceRequest $request)
    {
        /* @var Invoice $invoice */
        $invoice = Invoice::findOrNew($request->invoice_id);
        $existed = $invoice->exists;
        if ($existed && $request->cancel == 1) {
            $invoice->entries()->delete();
            $invoice->setStatus(Invoice::STATUS_CANCELLED)->save();
            return redirect('invoice')->with(
                'status', trans('redbill.invoice_nr_cancelled', ['nr' => $invoice->number])
            );
        }
        $invoice->fill($request->data)->save();
        if ($request->data['date_payed']) {
            // Set to payed if date_payed is set
            $invoice->setStatus(Invoice::STATUS_PAYED)->save();
        } elseif (!$invoice->status || $invoice->status == Invoice::STATUS_PAYED) {
            // Set to open if is payed and no date_payed set
            $invoice->setStatus(Invoice::STATUS_OPEN)->save();
        }
        if (!$invoice->number) {
            $invoice->setAttribute('number', $invoice->getNumber())->save();
        }
        if (!$existed) {
            return redirect('invoice/edit/' . $invoice->id);
        } else {
            // Set order + delivery date to oldest/newest entry
            /* @var Collection $entries */
            $entries = $invoice->sortedEntries();
            if ($entries->count()) {
                // Adjust order/deliver date to match entries
                $invoice->setAttribute('date_ordered', $entries->first()->asset->delivery_date)
                    ->setAttribute('date_delivered', $entries->last()->asset->delivery_date)
                    ->save();
            }
            return redirect('invoice')->with('status', trans('redbill.invoice_nr_saved', ['nr' => $invoice->number]));
        }
    }

    public function getPdf(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = \PDF::getDomPDF();
        $pdf->load_html(\View::make('invoice.pdf', compact('invoice'))->render());
        $pdf->render();

        // Place page counter on center bottom with 2.5cm margin
        $canvas = $pdf->get_canvas();
        $text = trans('redbill.page') . " {PAGE_NUM} / {PAGE_COUNT}";
        $w = $pdf->get_canvas()->get_width();
        $h = $pdf->get_canvas()->get_height();
        $tw = $canvas->get_text_width('Page 1 / 2', 'verdana', 6);
        $th = $canvas->get_font_height('verdana', 6);
        $canvas->page_text(
            ($w / 2 - $tw / 2), ($h - $th - 25), $text, 'verdana', 6
        );
        $pdf->stream($invoice->number . '.pdf', ['Attachment' => 0]);
        exit;
    }

    public function addAssets(Request $request)
    {
        /* @var Invoice $invoice */
        $invoice = Invoice::findOrFail($request->invoice_id);
        foreach ($request->assets as $newInvoiceEntry) {
            $entry = new InvoiceEntry();
            $entry->fill($newInvoiceEntry);
            $invoice->entries()->save($entry);
        }
    }

    public function removeEntries(Request $request)
    {
        /* @var Invoice $invoice */
        $invoice = Invoice::findOrFail($request->invoice_id);
        foreach ($request->entries as $row) {
            $invoice->entries()->findOrNew($row['entry_id'])->delete();
        }
    }

    public function reopen(Request $request, $id)
    {
        /* @var Invoice $invoice */
        $invoice = Invoice::findOrFail($id);
        $invoice->setStatus(Invoice::STATUS_OPEN)
            ->setAttribute('date_payed', null)
            ->save();
        return redirect('/invoice/edit/' . $invoice->id);
    }

    public function setPayed(Request $request, $id)
    {
        /* @var Invoice $invoice */
        $invoice = Invoice::findOrFail($id);
        $invoice->setStatus(Invoice::STATUS_PAYED)
            ->setAttribute('date_payed', date('Y-m-d'))
            ->save();
        return redirect('invoice')->with('status', trans('redbill.invoice_set_payed', ['nr' => $invoice->number]));
    }

    public function view(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->setAttribute('viewOnly', true);
        return view('invoice/view', compact('invoice'));
    }

    private function _getTimeSpans()
    {
        $list = [];
        $years = [];
        foreach ($this->invoices->getPayedYears() as $payedYear) {
            /* @var Invoice $payedYear */
            $years[] = \DateTime::createFromFormat('Y-m-d', $payedYear->date_payed)->format('Y');
        }
        $years = array_unique($years);
        foreach ($years as $year) {
            if ((int)date('Y') - (int)$year > 2) {
                // Put into "more" entry if > 2 years ago
                $list['more'][] = [
                    'label' => trans('redbill.full_year', ['year' => $year]),
                    'from'  => "$year-01-01",
                    'to'    => "$year-12-31",
                ];
            } else {
                $list[$year] = [
                    [
                        'label' => trans('redbill.full_year', ['year' => $year]),
                        'from'  => "$year-01-01",
                        'to'    => "$year-12-31",
                    ],
                    [
                        'label' => trans('redbill.quarter_1_of_year', ['year' => $year]),
                        'from'  => "$year-01-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-03-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.january') . " $year",
                        'from'  => "$year-01-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-01-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.february') . " $year",
                        'from'  => "$year-02-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-02-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.march') . " $year",
                        'from'  => "$year-03-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-03-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.quarter_2_of_year', ['year' => $year]),
                        'from'  => "$year-04-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-06-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.april') . " $year",
                        'from'  => "$year-04-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-04-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.may') . " $year",
                        'from'  => "$year-05-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-05-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.june') . " $year",
                        'from'  => "$year-06-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-06-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.quarter_3_of_year', ['year' => $year]),
                        'from'  => "$year-07-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-09-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.july') . " $year",
                        'from'  => "$year-07-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-07-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.august') . " $year",
                        'from'  => "$year-08-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-08-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.september') . " $year",
                        'from'  => "$year-09-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-09-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.quarter_4_of_year', ['year' => $year]),
                        'from'  => "$year-10-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-12-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.october') . " $year",
                        'from'  => "$year-10-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-10-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.november') . " $year",
                        'from'  => "$year-11-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-11-01")->format('Y-m-t'),
                    ],
                    [
                        'label' => trans('redbill.december') . " $year",
                        'from'  => "$year-12-01",
                        'to'    => \DateTime::createFromFormat('Y-m-d', "$year-12-01")->format('Y-m-t'),
                    ],
                ];
            }
        }
        // Add "Show all"
        $list['more'][] = [
            'label' => trans('redbill.show_all'),
            'from'  => end($years) . "-01-01",
            'to'    => reset($years) . "-12-31",
        ];
        return $list;
    }
}
