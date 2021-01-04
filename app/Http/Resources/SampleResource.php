<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SampleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'redirections' => $this->redirections,
            'total_time' => $this->time,
            'check_at' => $this->checkStartTime($this->created_at->timestamp, $this->time)
        ];
    }

    private function checkStartTime(int $start, float $delay)
    {
        return Carbon::createFromTimestamp($start - $delay)->toTimeString(); 
    }
}
