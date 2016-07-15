<?php

function getInvoiceRowClasses(\Redbill\Invoice $invoice)
{
    $rowBg = [];
    if ($invoice->isOverdue()) {
        $rowBg[] = 'danger';
    }
    if ($invoice->status == \Redbill\Invoice::STATUS_CANCELLED) {
        $rowBg[] = 'rb-lgrey';
    }
    return ' ' . implode(' ', $rowBg);
}

function invoiceSpan()
{
    $from = DateTime::createFromFormat('Y-m-d', Session::get('invoiceFrom'))->format('d.m.Y');
    $to = DateTime::createFromFormat('Y-m-d', Session::get('invoiceTo'))->format('d.m.Y');
    return "$from - $to";
}