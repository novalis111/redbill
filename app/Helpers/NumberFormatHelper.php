<?php

function formatCurrency($sum)
{
    switch (App::getLocale()) {
        case 'de':
            $sum = number_format($sum, 2, ',', '.');
            break;
        default:
            $sum = number_format($sum, 2);
            break;
    }
    return sprintf(config('redbill.currency_format'), $sum);
}

function formatDate($date)
{
    return date('d.m.Y', strtotime($date));
}

function formatTaxRate($rate)
{
    return $rate . ' %';
}

function formatFloat($float, $digits = 1)
{
    switch (App::getLocale()) {
        case 'de':
            $float = number_format($float, $digits, ',', '.');
            break;
        default:
            $float = number_format($float, $digits);
            break;
    }
    return $float;
}