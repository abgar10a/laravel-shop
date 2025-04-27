<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateRatingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reviewableIds;

    public $reviewableType;

    public $tableName;

    public function __construct($reviewableIds, $reviewableType, $tableName)
    {
        $this->reviewableIds = $reviewableIds;
        $this->reviewableType = $reviewableType;
        $this->tableName = $tableName;
    }

    public function handle(): void
    {
        logger("$this->reviewableType --- $this->tableName");
        foreach ($this->reviewableIds as $id) {
            $averageRating = DB::table('reviews')
                ->where(function ($query) use ($id) {
                    return $query->where('reviewable_type', $this->reviewableType)
                        ->where('reviewable_id', $id);
                })
                ->avg('rating');

            $averageRating = $averageRating !== null ? round($averageRating, 1) : 0;

            DB::table($this->tableName)
                ->where('id', $id)
                ->update(['rating' => $averageRating]);
        }
    }
}
