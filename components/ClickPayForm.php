<?php namespace Shohabbos\Click\Components;

use Cms\Classes\ComponentBase;
use Shohabbos\Click\Models\Settings;

/**
 * User session
 *
 * This will inject the user object to every page and provide the ability for
 * the user to sign out. This can also be used to restrict access to pages.
 */
class ClickPayForm extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'CLick Pay Form',
            'description' => 'Will be show click pay form component'
        ];
    }


    public function onRun() {
        $this->page['service_id']       = Settings::get('service_id');
        $this->page['merchant_id']      = Settings::get('merchant_id');
        $this->page['secret_key']       = Settings::get('secret_key');        
        $this->page['merchant_user_id'] = Settings::get('merchant_user_id');	
    }

    public static function generateUrl($transaction_param, $amount, $return_url) {
        $service_id = Settings::get('service_id');
        $merchant_id = Settings::get('merchant_id');

        return "https://my.click.uz/services/pay?service_id={$service_id}&merchant_id={$merchant_id}&amount={$amount}&transaction_param={$transaction_param}&return_url={$return_url}";
    } 
    

}
