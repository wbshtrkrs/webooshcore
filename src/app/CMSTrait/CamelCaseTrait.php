<?php

namespace App\CMSTrait;

trait CamelCaseTrait
{
    /**
     * @var array
     */
    public $originalData = [];

    /**
     * @var array
     */
    public $updatedData = [];

    /**
     * @var array
     */
    private $dontKeep = [];

    /**
     * @var array
     */
    private $doKeep = [];

    /**
     * @var bool
     */
    public $updating = false;

    /**
     * @var array
     */
    public $dirtyData = [];

    /**
     * Init auditing.
     */
    public static function bootAuditingTrait()
    {
        /*static::created(function ($model) {
            $model->prepareAudit();
            if (sizeof($model->dirtyData) == 0) return;
            $originalData = json_encode($model->originalData);
            $updatedData = json_encode($model->updatedData);
            $dirtyData = json_encode($model->dirtyData);
            app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new AuditJob($model, 'created', $originalData, $updatedData, $dirtyData));
        });*/

        static::updating(function ($model) {
            $now = new \Carbon\Carbon();
            $created_at = $model->created_at;

            if (!empty($model->created_at) && $created_at->diffInMinutes($now) < 1) return;

            $model->prepareAudit();
            if (!empty($model->created_at) && $created_at->diffInMinutes($now) < 5) {
                foreach($model->dirtyData as $dirtyDataKey=>$dirtyData){
                    if ( !isset($model->originalData[$dirtyDataKey]) || empty($model->originalData[$dirtyDataKey]) ){
                        unset($model->dirtyData[$dirtyDataKey]);
                    }
                }
            }

            if (sizeof($model->dirtyData) == 0) return;
            $originalData = json_encode($model->originalData);
            $updatedData = json_encode($model->updatedData);
            $dirtyData = json_encode($model->dirtyData);
            app(Dispatcher::class)->dispatch(new AuditJob($model, 'saved', $originalData, $updatedData, $dirtyData, new \DateTime()));
        });

        /*static::deleted(function ($model) {
            $model->prepareAudit();
            if (sizeof($model->dirtyData) == 0) return;
            $originalData = json_encode($model->originalData);
            $updatedData = json_encode($model->updatedData);
            $dirtyData = json_encode($model->dirtyData);
            app('Illuminate\Contracts\Bus\Dispatcher')->dispatch(new AuditJob($model, 'deleted', $originalData, $updatedData, $dirtyData));
        });*/
    }

    /**
     * Get list of logs.
     *
     * @return mixed
     */
    public function logs()
    {
        return $this->morphMany(Log::class, 'owner');
    }

    public function getLogs()
    {
        list($type, $id) = $this->getMorphs('owner', null, null);
        $type_value = get_class($this);
        $id_value = $this->getKey();

        return Log::where([$type=>$type_value, $id=>$id_value])->get();
    }

    /**
     * Generates a list of the last $limit revisions made to any objects
     * of the class it is being called from.
     *
     * @param int    $limit
     * @param string $order
     *
     * @return mixed
     */
    public static function classLogHistory($limit = 100, $order = 'desc')
    {
        return Log::where('owner_type', get_called_class())
            ->orderBy('updated_at', $order)->limit($limit)->get();
    }

    /**
     * @param int    $limit
     * @param string $order
     *
     * @return mixed
     */
    public function logHistory($limit = 100, $order = 'desc')
    {
        return static::classLogHistory($limit, $order);
    }

    /**
     * Prepare audit model.
     */
    public function prepareAudit()
    {
        if (!isset($this->auditEnabled) || $this->auditEnabled) {
            $this->originalData = $this->original;
            $this->updatedData = $this->attributes;

            foreach ($this->updatedData as $key => $val) {
                if (gettype($val) == 'object' && !method_exists($val, '__toString')) {
                    unset($this->originalData[$key]);
                    unset($this->updatedData[$key]);
                    array_push($this->dontKeep, $key);
                }
            }

            // Dont keep log of
            $this->dontKeep = isset($this->dontKeepLogOf) ?
                $this->dontKeepLogOf + $this->dontKeep
                : $this->dontKeep;

            // Keep log of
            $this->doKeep = isset($this->keepLogOf) ?
                $this->keepLogOf + $this->doKeep
                : $this->doKeep;

            unset($this->attributes['dontKeepLogOf']);
            unset($this->attributes['keepLogOf']);

            // Get changed data
            $this->dirtyData = [];
            foreach($this->getDirty() as $key=>$dirty){
                if ($this->isAuditing($key)) $this->dirtyData[$key] = $dirty;
            }
            // Tells whether the record exists in the database
            $this->updating = $this->exists;
        }
    }

    /**
     * Audit creation.
     *
     * @return void
     */
    public function auditCreation($date)
    {
        if (isset($this->historyLimit) && $this->logHistory()->count() >= $this->historyLimit) {
            $LimitReached = true;
        } else {
            $LimitReached = false;
        }
        if (isset($this->logCleanup)) {
            $LogCleanup = $this->LogCleanup;
        } else {
            $LogCleanup = false;
        }

        if (((!isset($this->auditEnabled) || $this->auditEnabled)) && (!$LimitReached || $LogCleanup)) {
            $log = ['old_value' => null];
            $log['new_value'] = [];

            foreach ($this->updatedData as $key => $value) {
                if ($this->isAuditing($key)) {
                    $log['new_value'][$key] = $value;
                }
            }

            $currDate = $date;

            $this->audit($log, 'created', $currDate);
        }
    }

    /**
     * Audit updated.
     *
     * @return void
     */
    public function auditUpdate($date)
    {
        if (isset($this->historyLimit) && $this->logHistory()->count() >= $this->historyLimit) {
            $LimitReached = true;
        } else {
            $LimitReached = false;
        }
        if (isset($this->logCleanup)) {
            $LogCleanup = $this->LogCleanup;
        } else {
            $LogCleanup = false;
        }

        if (((!isset($this->auditEnabled) || $this->auditEnabled)) && (!$LimitReached || $LogCleanup)) {
            $changes_to_record = $this->changedAuditingFields();
            if (count($changes_to_record)) {
                foreach ($changes_to_record as $key => $change) {
                    $log['old_value'][$key] = array_get($this->originalData, $key);
                    $log['new_value'][$key] = array_get($this->updatedData, $key);
                }

                $currDate = $date;

                $this->audit($log, 'updated', $currDate);
            }
        }
    }

    /**
     * Audit deletion.
     *
     * @return void
     */
    public function auditDeletion($date)
    {
        if (isset($this->historyLimit) && $this->logHistory()->count() >= $this->historyLimit) {
            $LimitReached = true;
        } else {
            $LimitReached = false;
        }
        if (isset($this->logCleanup)) {
            $LogCleanup = $this->LogCleanup;
        } else {
            $LogCleanup = false;
        }

        if (((!isset($this->auditEnabled) || $this->auditEnabled) && $this->isAuditing('deleted_at')) && (!$LimitReached || $LogCleanup)) {
            $log = ['new_value' => null];

            foreach ($this->updatedData as $key => $value) {
                if ($this->isAuditing($key)) {
                    $log['old_value'][$key] = $value;
                }
            }

            $currDate = $date;

            $this->audit($log, 'deleted', $currDate);
        }
    }

    /**
     * Audit model.
     *
     * @return Log
     */
    public function audit(array $log, $type, $date)
    {
        foreach($log['new_value'] as $key=>$value){
			$logAuditing = [
				'old_value'  => $log['old_value'][$key],
				'new_value'  => $log['new_value'][$key],
				'key'		 => $key,
				'owner_type' => get_class($this),
				'owner_id'   => $this->getKey(),
				'user_id'    => $this->getUserId(),
				'type'       => $type,
				'created_at' => new \DateTime(),
				'updated_at' => new \DateTime(),
                'log_date' => $date,
			];
			Log::insert($logAuditing);
		};
    }

    /**
     * Get user id.
     *
     * @return null
     */
    protected function getUserId()
    {
        try {
            if (\Auth::check()) {
                return \Auth::user()->getAuthIdentifier();
            }
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Fields Changed.
     *
     * @return array
     */
    private function changedAuditingFields()
    {
        $changes_to_record = [];
        foreach ($this->dirtyData as $key => $value) {
            if ($this->isAuditing($key) && !is_array($value)) {
                // Check whether the current value is difetente the original value
                if (!isset($this->originalData[$key]) || $this->originalData[$key] != $this->updatedData[$key]) {
                    $changes_to_record[$key] = $value;
                }
            } else {
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
            }
        }

        return $changes_to_record;
    }

    /**
     * Is Auditing?
     *
     * @param $key
     *
     * @return bool
     */
    private function isAuditing($key)
    {
        // Checks if the field is in the collection of auditable
        if (isset($this->doKeep) && in_array($key, $this->doKeep)) {
            return true;
        }

        // Checks if the field is in the collection of non-auditable
        if (isset($this->dontKeep) && in_array($key, $this->dontKeep)) {
            return false;
        }

        // Checks whether the auditable list is clean
        return empty($this->doKeep);
    }

    /**
     * Idenfiable name.
     *
     * @return mixed
     */
    public function identifiableName()
    {
        return $this->getKey();
    }

    /**
     * Verify is type auditable.
     *
     * @param $key
     *
     * @return bool
     */
    public function isTypeAuditable($key)
    {
        $auditableTypes = isset($this->auditableTypes)
                          ? $this->auditableTypes
                          : ['created', 'saved', 'deleted'];

        // Checks if the type is in the collection of type-auditable
        if (in_array($key, $auditableTypes)) {
            return true;
        }
    }
}
