<?php

namespace App\Observers;

use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

class ActionLogObserver
{
    /**
     * Handle the model "created" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function created($model)
    {
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model_affected' => get_class($model),
            'affected_id' => $model->id,
            'description' => 'Created a new record',
        ]);
    }

    /**
     * Handle the model "updated" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function updated($model)
    {
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model_affected' => get_class($model),
            'affected_id' => $model->id,
            'description' => 'Updated a record',
        ]);
    }

    /**
     * Handle the model "deleted" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleted($model)
    {
        ActionLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model_affected' => get_class($model),
            'affected_id' => $model->id,
            'description' => 'Deleted a record',
        ]);
    }
}
