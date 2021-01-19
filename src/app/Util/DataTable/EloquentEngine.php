<?php

namespace App\Util\VodeaCore\DataTable;

use App\Service\VodeaCore\ExportService;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Yajra\DataTables\EloquentDataTable;

class EloquentEngine extends EloquentDataTable {
    protected $doPostAction;
    protected $doCustomOrQuery;
    protected $exludedSearchColumns = [];
    protected $defaultOrderCallback;
    protected $headerExports = [];
    protected $exportFilename = '';

    protected function wrap($column) {
        if (count(explode('.', $column)) <= 1 && strpos($column, '(') !== false) return $this->connection->getQueryGrammar()->wrap($column); else
            return $column;
    }

    public function postAction($postAction) {
        $this->doPostAction = $postAction;
        return $this;
    }

    public function excludeSearch($columns){
        foreach($columns as $column){
            $this->exludedSearchColumns[] = $column;
            if ($this->isAlias($column)) $this->exludedSearchColumns[] = $this->getColumnNameByAlias($column);
        }
        return $this;
    }

    public function headerExports($columns){
        $this->headerExports = array_merge($this->headerExports, $columns);
        return $this;
    }

    public function make($mDataSupport = false, $orderFirst = false) {
        $response = parent::make($mDataSupport, $orderFirst);
        $data = $response->getData();

        if (!empty($this->doPostAction)){
            $postAction = $this->doPostAction;
            $postData = $postAction($data->data);
        }

        $data->data = empty($postData) ? $data->data : $postData;

        if (!empty( $this->request->get('datatableAction') )) {
            return $this->export($data->data);
        }

        $response->setData($data);
        return $response;
    }
    public function filtering() {
        $mainQuery = $this->query;
        $this->query->where(
            function ($query) use($mainQuery) {
                $globalKeyword = $this->request->keyword();
                $queryBuilder  = $this->getQueryBuilder($query);

                foreach ($this->request->searchableColumnIndex() as $index) {
                    $columnName = $this->getColumnName($index);
                    if ($this->isBlacklisted($columnName)) {
                        continue;
                    }

                    if (in_array($columnName, $this->exludedSearchColumns)) continue;

                    // check if custom column filtering is applied
                    if (isset($this->columnDef['filter'][$columnName])) {
                        $columnDef = $this->columnDef['filter'][$columnName];
                        // check if global search should be applied for the specific column
                        $applyGlobalSearch = count($columnDef['parameters']) == 0 || end($columnDef['parameters']) !== false;
                        if (! $applyGlobalSearch) {
                            continue;
                        }

                        if ($columnDef['method'] instanceof Closure) {
                            $whereQuery = $queryBuilder->newQuery();
                            call_user_func_array($columnDef['method'], [$whereQuery, $globalKeyword]);
                            $queryBuilder->addNestedWhereQuery($whereQuery, 'or');
                        } else {
                            $this->compileColumnQuery(
                                $queryBuilder,
                                Helper::getOrMethod($columnDef['method']),
                                $columnDef['parameters'],
                                $columnName,
                                $globalKeyword
                            );
                        }
                    } else {
                        if (count(explode('.', $columnName)) > 1) {
                            $eagerLoads     = $this->getEagerLoads();
                            $parts          = explode('.', $columnName);
                            $relationColumn = array_pop($parts);
                            $relation       = implode('.', $parts);
                            if (in_array($relation, $eagerLoads)) {
                                $this->compileRelationSearch(
                                    $queryBuilder,
                                    $relation,
                                    $relationColumn,
                                    $globalKeyword
                                );
                            } else {
                                $this->compileQuerySearch($queryBuilder, $columnName, $globalKeyword);
                            }
                        } else {
                            $this->compileQuerySearch($queryBuilder, $columnName, $globalKeyword);
                        }
                    }

                    $this->isFilterApplied = true;
                }

                if (!empty($this->doCustomOrQuery)){
                    $doCustomOrQuery = $this->doCustomOrQuery;
                    $doCustomOrQuery($queryBuilder);
                }
            }
        );
    }

    protected function getColumnName($index, $wantsAlias = false) {
        $entity = get_class($this->query->getModel());
        $indexList = $entity::INDEX_FIELD;
        if(isset($indexList[$index]) && $this->getColumnNameByAlias($indexList[$index]) == $indexList[$index]) return $indexList[$index];

        $column = $this->request->columnName($index);

        // DataTables is using make(false)
        if (is_numeric($column)) {
            $column = $this->getColumnNameByIndex($index);
        }

        if($column instanceof Expression){
            $column = $column->getValue();
        }

        if (Str::contains(Str::upper($column), ' ')) {
            $column = $this->extractColumnName($column, $wantsAlias);
            if ($this->isAlias($column)) $column = $this->getColumnNameByAlias($column);
        }

        return $column;
    }

    protected function getColumnNameByAlias($alias) {
        if (!empty($this->columns)) {
            foreach($this->columns as $column) {
                if($alias == $this->extractColumnName($alias, true)) return $this->extractColumnName($alias, false);
            }
        }

        return $alias;
    }

    protected function extractColumnName($str, $wantsAlias) {
        $str = str_replace(' AS ', ' ', $str);
        $str = str_replace(' as ', ' ', $str);

        $columnParts = explode(' ', $str);

        if(!empty($columnParts) && count($columnParts) > 1) {
            $columnAlias = end($columnParts);
            $columnName = substr($str, 0, -(strlen($columnAlias)+1));
            if($wantsAlias) {
                return $columnAlias;
            } else {
                return $columnName;
            }
        } elseif(strpos($str, '.')) {
            $columnNameParts = explode('.', $str);
            $columnName = end($columnNameParts);
            return $columnName;
        }

        return $str;
    }

    private function isAlias($columnNameOrAlias){
        $columnName = $this->getColumnNameByAlias($columnNameOrAlias);
        if ($columnNameOrAlias == $columnName) return false;

        return true;
    }

    public function customOrQuery($customOrQuery) {
        $this->doCustomOrQuery = $customOrQuery;
        return $this;
    }

    public function paging() {
        if (!empty( $this->request->get('datatableAction') )) return;
        parent::paging();
    }
    public function export($data){
        $action = $this->request->get('datatableAction');

        $headers = $this->headerExports;
        if (empty($headers) || count($headers) == 0) $headers = (array)json_decode($this->request->get('headers'));
        if (empty($headers) || count($headers) == 0) $headers = getDataTableHeader(get_class($this->query->getModel()));
        $list = getDataTableList($data, $headers);
        $exportFilename = $this->exportFilename;
        if(empty($exportFilename)) $exportFilename = class_basename($this->query->getModel());
        $fileName = ExportService::ExportData($action, array_values($headers), $list, $exportFilename);
        return \Response::download($fileName)->deleteFileAfterSend(true);
    }

    public function exportFilename($exportFilename){
        $this->exportFilename = $exportFilename;
        return $this;
    }

    public function ordering() {
        if ($this->orderCallback) {
            call_user_func($this->orderCallback, $this->getQueryBuilder());
            return;
        }

        $orderableColumns = $this->request->input('order');
        if (empty($orderableColumns) && !empty($this->defaultOrderCallback)) {
            call_user_func($this->defaultOrderCallback, $this->getQueryBuilder());
            return;
        }

        if (empty($orderableColumns)) return;

        foreach ($orderableColumns as $orderableColumn) {
            $columnName = $orderableColumn['columnName'];

            if($this->isAlias($columnName)){
                $columnName = $this->getColumnNameByAlias($columnName);
                $this->getQuery()->orderByRaw($columnName . ' ' . $orderableColumn['dir']);
            } else {
                $this->getQuery()->orderBy($columnName, $orderableColumn['dir']);
            }
        }
    }

    public function defaultOrder(callable $closure) {
        $this->defaultOrderCallback = $closure;
        return $this;
    }

    protected function compileQuerySearch($query, $column, $keyword, $relation = 'or') {
        $column = $this->addTablePrefix($query, $column);
        if ($this->database === 'sqlsrv') {
            if(substr($column, 0, 5) == 'user.'){
                $column = '[user].'.substr($column, 5, strlen($column));
            };
        }
        $column = $this->castColumn($column);
        $sql    = $column . ' LIKE ?';

        if ($this->isCaseInsensitive()) {
            $sql = 'LOWER(' . $column . ') LIKE ?';
        }

        $query->{$relation . 'WhereRaw'}($sql, [$this->prepareKeyword($keyword)]);
    }

    protected function addTablePrefix($query, $column)
    {
        // Check if field does not have a table prefix
        if (strpos($column, '(') !== false) {
            return $column;
        }

        return parent::addTablePrefix($query, $column);
    }
}
