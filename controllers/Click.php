<?php namespace Shohabbos\Click\Controllers;

use Event;
use Illuminate\Routing\Controller;
use Shohabbos\Click\Models\Settings;
use Shohabbos\Click\Models\Transaction;


class Click extends Controller
{
    private $service_id;
    private $merchant_id;
    private $secret_key;
    private $merchant_user_id;

    public $params = [];

    public function __construct() {
        $this->service_id       = Settings::get('service_id');
        $this->merchant_id      = Settings::get('merchant_id');
        $this->secret_key       = Settings::get('secret_key');        
        $this->merchant_user_id = Settings::get('merchant_user_id');
    }



    public function index() {
        \Log::error($_REQUEST);

        $this->params = $_REQUEST;
        header('Content-Type: application/json');


        // check sign key
        if(!isset($this->params['sign_string']) || $this->params['sign_string'] !== $this->getSing()) {
            exit(json_encode(self::clickMessages()[1]));
        }

        // check action
        if(!in_array($this->params['action'], [0, 1])) {
            exit(json_encode(self::clickMessages()[3]));
        }


        // check account
        $result = false;
        $message = self::clickMessages()[5];
        Event::fire('shohabbos.click.existsAccount', [$this->params, &$result, &$message]);
        if (!$result) {
            exit(json_encode(self::clickMessages()[5]));
        }

        
        // Validate state of account or order
        $result = true;
        $message = 'Account or order state is not true';
        Event::fire('shohabbos.click.stateAccount', [$this->params, &$result, &$message]);
        if (!$result) {
            exit(json_encode(self::clickMessages()[2]));
        }


        // validate amount
        $amount = $this->params['amount'];
        $result = $amount >= Settings::get('min_amount', 1000) && $amount <= Settings::get('max_amount', 1000000);
        $message = self::clickMessages()[2];
        Event::fire('shohabbos.click.checkAmount', [$this->params, $amount, &$result, &$message]);
        if (!$result) {
            exit(json_encode(self::clickMessages()[2]));
        }

        if($this->params['action'] == 0) {
            $this->prepare();
        } elseif($this->params['action'] == 1) {
            $this->confirm();
        }
    }

    private function prepare() {
        $transaction = Transaction::
            where('status', 0)->
            where('click_trans_id', $this->params['click_trans_id'])->first();

        if(!$transaction){
            $transaction = new Transaction();
            $transaction->status = "0";
            $transaction->amount = $this->params['amount'];
            $transaction->account = $this->params['merchant_trans_id'];
            $transaction->date = time();
            $transaction->click_trans_id = $this->params['click_trans_id'];
            $transaction->error = $this->params['error'];
            $transaction->save();
        }

        $return = array(
            'click_trans_id' => $transaction->click_trans_id,
            'merchant_trans_id' => $transaction->account,
            'merchant_prepare_id' => $transaction->id,
        );

        exit(json_encode(array_merge(
        	self::clickMessages()[0], 
        	$return
        )));
    }

    private function confirm() {
        $transaction = Transaction::find($this->params['merchant_prepare_id']);

        if($transaction) {
            if($this->params['error'] == "-1") {
                exit(json_encode(self::clickMessages()[4]));
            }

            if($transaction->status == '1') {
                exit(json_encode(self::clickMessages()[4]));
            }

            if($transaction->amount != $this->params['amount']) {
                exit(json_encode(self::clickMessages()[2]));
            }

            if($this->params['error'] == "-5017") {
                $transaction->error = "-5017";
                $transaction->save();
                exit(json_encode(self::clickMessages()[9]));
            }

            if($transaction->error == "-5017") {
                exit(json_encode(self::clickMessages()[9]));
            }

            // fill balance or update status order
            $result = false;
            $message = 'Unknown error';
            Event::fire('shohabbos.click.performTransaction', [$transaction, &$result, &$message]);
            if (!$result) {
                exit(json_encode(self::clickMessages()[9]));
            }


            $transaction->status = "1";
            if($transaction->save()) {
                $return = array(
                    'click_trans_id'=> $transaction->click_trans_id,
                    'merchant_trans_id' => $transaction->account,
                    'merchant_confirm_id' => $transaction->id
                );
                exit(json_encode(array_merge(self::clickMessages()[0], $return)));                        
            }

            exit(json_encode(self::clickMessages()[9]));
        } else {
            exit(json_encode(self::clickMessages()[6]));
        }
    }



    private function getSing() {
        $signString = ($this->params['click_trans_id'] .
            $this->params["service_id"] .
            $this->secret_key .
            $this->params['merchant_trans_id'] .
            ($this->params['action'] == 1 ? $this->params['merchant_prepare_id'] : '').
            $this->params['amount'] .
            $this->params['action'] .
            $this->params['sign_time']);

        //dump($signString);

        return md5($signString);
    }

    public static function clickMessages() {
        return [
            ["error" => "0", "error_note" => "Success"],
            ["error" => "-1", "error_note" => "SIGN CHECK FAILED"],
            ["error" => "-2", "error_note" => "Amount not correct"],
            ["error" => "-3", "error_note" => "Action not found"],
            ["error" => "-4", "error_note" => "Already paid"],
            ["error" => "-5", "error_note" => "User does not exist"],
            ["error" => "-6", "error_note" => "Transaction does not exist"],
            ["error" => "-7", "error_note" => "Failed to update user"],
            ["error" => "-8", "error_note" => "Error in request from click"],
            ["error" => "-9", "error_note" => "Transaction cancelled"]
        ];
    }
    
}
