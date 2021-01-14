<?php
namespace App\Service\WebooshCore;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;

class ExportService {
	public static function ExportData($action, $headerList, $dataList, $fileName = 'Export'){
		$dataList = static::FixNumericString($dataList);
		if($action == 'xlsx') {
			$writer = WriterFactory::create(Type::XLSX);
			$dataType = '.xlsx';
		} else if($action == 'csv') {
			$writer = WriterFactory::create(Type::CSV);
			$writer->setShouldAddBOM(false);
			$dataType = '.csv';
		}
		if(isset($dataType)) {
			$filePath = 'assets/upload/temp/';
			$folderDirectory = public_path().'/'.$filePath;
			if (!file_exists($folderDirectory)) {
				mkdir($folderDirectory, 0777, true);
			}
			$fileName = $filePath.$fileName.date('_dmY_his').$dataType;
			$writer->openToFile($fileName);

			if($action == 'csv') $writer->addRow(['sep=,']);
			if (!empty($headerList) && count($headerList) > 0) $writer->addRow($headerList);
			foreach($dataList as $row) {
				$writer->addRow($row);
			}
			$writer->close();
		}
		return $fileName;
	}

	private static function GetThousandSeparator(){
		$thousandSeparator = config('CMS.thousandSeparator');
		return empty($thousandSeparator) ? '.' : $thousandSeparator;
	}
	private static function GetDecimalSeparator(){
		$decimalSeparator = config('CMS.decimalSeparator');
		return empty($decimalSeparator) ? ',' : $decimalSeparator;
	}
	private static function FixNumericString($listOfArray){
		$thousandSeparator = static::GetThousandSeparator();
		$decimalSeparator = static::GetDecimalSeparator();
		foreach($listOfArray as &$arrayItem){
			foreach($arrayItem as &$item){
				$itemString = str_replace(array($thousandSeparator, $decimalSeparator), array('', '.'), $item);
				$floatItem = floatval( $itemString );
				if ($floatItem . '' == $itemString) $item = $floatItem;
			}

		}
		return $listOfArray;
	}
}
