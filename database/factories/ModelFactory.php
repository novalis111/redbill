<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(
    Redbill\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
}
);

$factory->define(
    Redbill\Asset::class, function (Faker\Generator $faker) {
    $type = $faker->randomElement([\Redbill\Asset::TYPE_REDBILL_TIME, \Redbill\Asset::TYPE_REDBILL_PRODUCT]);
    if ($type == \Redbill\Asset::TYPE_REDBILL_PRODUCT) {
        $title = $faker->randomElement(['Unobtanium', 'Turtles', 'Special Ingredient']);
        $amount = $faker->numberBetween(1, 7);
        $unit = \Redbill\Asset::UNIT_PIECES;
        $comment = 'Sold ' . $faker->randomElement(['in store', 'online', 'at night to an unnamed guy']);
    } else {
        $title = $faker->randomElement(['Database', 'Frontend', 'Backend']) . ' '
            . $faker->randomElement(['Maintenance', 'Bugfix', 'Design', 'QA']);
        $amount = $faker->randomFloat(1, 0.1, 7);
        $unit = \Redbill\Asset::UNIT_HOURS;
        $comment = $faker->randomElement(['Added', 'Removed', 'Fixed']) . ' '
            . $faker->randomElement(['User', 'Database', 'Frontend', 'Backend']) . ' '
            . $faker->randomElement(['Roles', 'Issue', 'GUI', 'Bug']);
    }
    return [
        'foreign_id'    => 0,
        'client_id'     => $faker->numberBetween(2, \Redbill\Company::max('id')),
        'type'          => $type,
        'title'         => $title,
        'amount'        => $amount,
        'unit'          => $unit,
        'delivery_date' => $faker->dateTimeBetween('-2 years'),
        'comment'       => $comment,
    ];
}
);

$factory->define(
    Redbill\Company::class, function (Faker\Generator $faker) {
    return [
        'company_name'  => $faker->company,
        'salutation'    => $faker->randomElement([$faker->titleFemale, $faker->titleMale]),
        'name'          => $faker->name,
        'street'        => $faker->streetName,
        'street_number' => $faker->numberBetween(1, 149),
        'postcode'      => $faker->postcode,
        'city'          => $faker->city,
        'country'       => $faker->country,
        'telephone'     => $faker->phoneNumber,
        'mobile'        => $faker->phoneNumber,
        'fax'           => $faker->phoneNumber,
        'email'         => $faker->safeEmail,
        'website'       => parse_url($faker->url, PHP_URL_HOST),
        'tax_number'    => $faker->creditCardNumber,
        'iban'          => $faker->iban($faker->countryCode),
        'bic'           => $faker->swiftBicNumber,
        'bank_name'     => 'Bank of ' . $faker->country,
    ];
}
);

$factory->define(
    Redbill\Invoice::class, function (Faker\Generator $faker) {
    $ownerId = 1;
    $clientId = $faker->numberBetween(2, \Redbill\Company::max('id'));
    /* @var Redbill\Company $client */
    $client = \Redbill\Company::find($clientId);
    $status = $faker->randomElement(\Redbill\Invoice::getStatuses());
    if ($status == \Redbill\Invoice::STATUS_CANCELLED && $faker->boolean(70)) {
        $status = \Redbill\Invoice::STATUS_OPEN;
    }
    // date_ordered
    $dateOrdered = $faker->dateTimeBetween('-2 years', '-5 days');
    $diffOrdered = $dateOrdered->diff(new DateTime());
    // date_delivered
    $dateDelivered = clone $dateOrdered;
    $dateDelivered->add(new DateInterval('P' . $faker->numberBetween(0, min($diffOrdered->days, 28)) . 'D'));
    $diffDelivered = $dateDelivered->diff(new DateTime());
    // date_billed
    $dateBilled = clone $dateDelivered;
    $diffBilled = $dateBilled->diff(new DateTime());
    $dateBilled->add(new DateInterval('P' . $faker->numberBetween(0, min($diffDelivered->days, 28)) . 'D'));
    // date_payed
    $datePayed = '';
    if ($diffBilled->days > 35 && $status != \Redbill\Invoice::STATUS_CANCELLED) {
        $status = \Redbill\Invoice::STATUS_PAYED;
        $datePayed = clone $dateBilled;
        $datePayed->add(new DateInterval('P' . $faker->numberBetween(0, min($diffBilled->days, 28)) . 'D'));
    }
    return [
        'owner_id'       => $ownerId,
        'client_id'      => $clientId,
        'number'         => date(config('redbill.invoice_prefix'), $dateBilled->getTimestamp())
            . $faker->unique()->numberBetween(123, 4567),
        'title'          => trans(config('redbill.invoice_title')) . ' ' . $client->company_name,
        'status'         => $status,
        'date_ordered'   => $dateOrdered,
        'date_delivered' => $dateDelivered,
        'date_billed'    => $dateBilled,
        'date_payed'     => $datePayed,
    ];
}
);