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
        } catch (\Exception $ex) {
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
		$successQuery = Transaction::where('error', 0);
		$failQuery = Transaction::where('error', '!=', 0);

		if ($this->property('days')) {
			$day = (time() - ($this->property('days') * 86400));
			$this->vars['success'] = $successQuery->where('date', '>', $day)->get();
			$this->vars['fail'] = $failQuery->where('date', '>', $day)->get();
		} else {
			$this->vars['success'] = $successQuery->get();
			$this->vars['fail'] = $failQuery->get();
		}
    }


}