<?php

use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            $assetRepo = new \Redbill\Repositories\AssetRepository();
            $invoices = new \Redbill\Repositories\InvoiceRepository();
            while ($invoices->open()->count() < 10) {
                factory(Redbill\Invoice::class, 10)->create();
            }
            foreach (\Redbill\Invoice::all() as $invoice) {
                /* @var \Redbill\Invoice $invoice */
                $maxEntries = rand(3, 11);
                $entryCount = 0;
                // Bill hours from assets to invoice entries
                $allAssets = $assetRepo->forClient($invoice->client->id);
                foreach ($allAssets as $idx => $asset) {
                    /* @var \Redbill\Asset $asset */
                    if (DateTime::createFromFormat('Y-m-d', $asset->delivery_date)->diff(
                            DateTime::createFromFormat('Y-m-d', $invoice->date_delivered)
                        )->days < 0
                    ) {
                        // Do not bill entries that are newer than delivery date
                        continue;
                    }
                    if ($asset->getAmountLeft() <= 0) {
                        // Do not bill entries that already got billed
                        unset($allAssets[$idx]);
                        continue;
                    }
                    $invoiceEntry = new \Redbill\InvoiceEntry(
                        [
                            'id'       => null,
                            'asset_id' => $asset->id,
                            'title'    => $asset->title,
                            'amount'   => $asset->amount,
                            'price'    => rand(5, 15) * 5,
                            'tax_rate' => 19,
                        ]
                    );
                    $invoice->entries()->save($invoiceEntry);
                    if (++$entryCount >= $maxEntries) {
                        break;
                    }
                }
                if ($invoice->entries->count() == 0) {
                    $invoice->setStatus(\Redbill\Invoice::STATUS_CANCELLED)
                        ->setAttribute('date_payed', '')
                        ->save();
                }
            }
            // Create some additional assets that do not belong to an invoice
            factory(Redbill\Asset::class, 50)->create();
        }
    }
}
