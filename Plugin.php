<?php namespace Shohabbos\Click;

use Backend;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{

    public function registerComponents()
    {
        return [
            \Shohabbos\Click\Components\ClickPayForm::class => 'ClickPayForm',
        ];
    }

    public function registerSettings()
    {
    	return [
	    	'transactions' => [
                'label'       => 'Transactions',
                'description' => 'Transactions of click',
                'icon'        => 'icon-list-alt',
                'url'         => Backend::url('shohabbos/click/transactions'),
                'order'       => 500,
                'category'    => 'Click',
                'keywords'    => 'click paymets',
                'permissions' => ['manage_click_transactions'],
            ],
	        'settings' => [
	        	'category'    => 'Click',
	            'label'       => 'Settings',
	            'description' => 'Settings of click',
	            'icon'        => 'icon-cog',
	            'class'       => 'Shohabbos\Click\Models\Settings',
	            'order'       => 501,
	            'keywords'    => 'click paymets',
                'permissions' => ['manage_click_settings'],
	        ]
	    ];
    }

}
