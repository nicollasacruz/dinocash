<?php

use App\Jobs\CloseAffiliateInvoice;
use App\Models\Invoice;
use Illuminate\Support\Facades\Queue;

test('close AffiliateInvoice', function () {
    // Dispatch the job to the queue
    CloseAffiliateInvoice::dispatch();

    // Ensure the job is processed (you may need to run the queue worker)
    Queue::assertPushed(CloseAffiliateInvoice::class);

    // Run the queue worker (assuming you are using the sync queue driver for testing)
    Queue::fake();
    Queue::assertPushed(CloseAffiliateInvoice::class);

    // Optionally, you can assert that the job has been processed
    Queue::assertPushed(CloseAffiliateInvoice::class, function ($job) {
        return $job instanceof CloseAffiliateInvoice;
    });

    // Sleep for a few seconds to allow the job to process
    sleep(5);

    // Retrieve the updated invoice
    dd(Invoice::first());
});

