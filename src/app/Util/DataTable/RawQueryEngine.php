<?php

namespace App\Util\WebooshCore\DataTable;

use App\Service\WebooshCore\ExportService;
use Yajra\DataTables\Request;

class RawQueryEngine extends EloquentEngine {
	public function __construct($model, Request $request) {
		$builder = $model->getValue();
		$this->query = $builder;
		$this->query_type = 'eloquent';
		$this->init($request, $builder);
	}

	protected function init($request, $builder, $type = 'builder'){
		$this->request    = $request;
		$this->query_type = $type;
		$this->columns    = $this->getColumns();
	}

	public function getColumns() {
		$columns = [];
		$rawColumns = explode(',', explode('from', substr(trim($this->query), 6))[0]);
		foreach($rawColumns as $rawColumn){
			$rawColumn = explode(' ', trim($rawColumn));
			$columns[] = $rawColumn[count($rawColumn) - 1];
		}
		return $columns;
	}

	public function paginate() {
		if ($this->request->isPaginationable() && ! $this->skipPaging) {
			$this->paging();
		}
	}

	public function count(){
		$myQuery = $this->query;
		$fromPosition = strrpos($myQuery, 'from');

		$count = 0;
		if ($fromPosition > -1){
			$myQuery = 'select count(*) count '.substr($myQuery, $fromPosition);
			$result = \DB::select($myQuery);
			return count($result) > 0 ? $result[0]->count : 0;
		}
		return $count;

	}
	public function ordering(){
		$orderList = $this->request->input('order');
		if (empty($orderList) || count($orderList) == 0) return;

		$this->query .= ' order by ';
		foreach($orderList as $index => $order){
			if (!isset($order['columnName']) || !isset($order['dir'])) continue;
			$this->query .= $index > 0 ? ' , ' : '';
			$this->query .= $order['columnName'] . ' ' . $order['dir'] . ' ' ;
		};
	}

	public function filterRecords(){
		parent::filterRecords();
	}
	public function paging() {
		if (!empty( $this->request->get('datatableAction') )) return;
		$start = $this->request->input('start');
		$length = $this->request->input('length');
		$this->query .= ' limit ' . $length . ' offset ' . $start . ' ';
	}
	public function results(){
		return \DB::select($this->query);
	}
	public function showDebugger(array $output){
		return $output;
	}
	public function exportFilename($exportFilename){
		return '';
	}
}
