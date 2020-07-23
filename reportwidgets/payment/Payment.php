<?php namespace Shohabbos\Click\ReportWidgets;

use Db;
use Exception;
use Carbon\Carbon;
use Backend\Classes\ReportWidgetBase;
use Shohabbos\Click\Models\Transaction;

class Payment extends ReportWidgetBase
{

    public function render()
    {
        try {
            $this->loadData();
        }
        catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('widget');
    }


    public function defineProperties()
	{
	    return [
	        'days' => [
	            'title'             => 'Oxirgi (x) kunlik hisobotni kortatish',
	            'default'           => '0',
	            'type'              => 'string',
	            'validationPattern' => '^[0-9]+$'
	        ]
	    ];
	}

	protected function loadData()
    {
		$successQuery = Transaction::where('state', 2);
		$failQuery = Transaction::where('state', '!=', 2);

		if ($this->property('days')) {
			$day = (time() - ($this->property('days') * 86400)) * 1000;
			$this->vars['success'] = $successQuery->where('create_time', '>', $day)->get();
			$this->vars['fail'] = $failQuery->where('create_time', '>', $day)->get();
		} else {
			$this->vars['success'] = $successQuery->get();
			$this->vars['fail'] = $failQuery->get();
		}
    }


}