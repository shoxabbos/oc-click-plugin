<?php
use RainLab\User\Models\User;

Event::listen('shohabbos.click.existsAccount', function ($params, &$result, &$message) {
    // find order or account
	$result = User::find($params['merchant_trans_id']);
});

// $result param by default is true
Event::listen('shohabbos.click.checkAmount', function ($params, $amount, &$result, &$message) {
    
});

Event::listen('shohabbos.click.performTransaction', function ($transaction, &$result, &$message) {
    // check order as paid or fill user balance
	$user = User::find($transaction->account);
	$user->balance += $transaction->amount;
	$result = $user->save();
});