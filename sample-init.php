<?php
use Shohabbos\BookShop\Models\Order;

Event::listen('shohabbos.click.existsAccount', function ($accounts, &$result, &$message) {
    // find order or account
	$result = Order::find($accounts['order_id']);
});

Event::listen('shohabbos.click.performTransaction', function ($transaction, &$result, &$message) {
    // check order as paid or fill user balance
	$order = Order::find($transaction->owner_id);
	$order->is_paid = 1;
	$result = $order->save();
});

Event::listen('shohabbos.click.cancelTransaction', function ($transaction, &$result, &$message) {
    // check order as cancelled or take away from user balance
	$order = Order::find($transaction->owner_id);
	$order->is_paid = 0;
	$result = $order->save();
});