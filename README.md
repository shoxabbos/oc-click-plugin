You can find documentation in here http://docs.click.uz/

### You can like this listen Plugin events and expand functionality 
```
<?php
use Shohabbos\BookShop\Models\Order;

Event::listen('shohabbos.payme.existsAccount', function ($accounts, &$result, &$message) {
    // find order or account
	$result = Order::find($accounts['order_id']);
});

// $result param by default is true
Event::listen('shohabbos.payme.checkAmount', function ($accounts, $amount, &$result, &$message) {
    // check amount
    $order = Order::find($accounts['order_id']);
    
    if ($order->amount != $amount) {
	$result = false;	
    }
});

Event::listen('shohabbos.payme.performTransaction', function ($transaction, &$result, &$message) {
    // check order as paid or fill user balance
	$order = Order::find($transaction->owner_id);
	$order->is_paid = 1;
	$result = $order->save();
});

Event::listen('shohabbos.payme.cancelTransaction', function ($transaction, &$result, &$message) {
    // check order as cancelled or take away from user balance
	$order = Order::find($transaction->owner_id);
	$order->is_paid = 0;
	$result = $order->save();
});
```

