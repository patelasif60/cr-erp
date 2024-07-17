<?php
  
namespace App\Imports;
  
use App\Jobs\JobProcessRLGLImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Log;
  
class RLGLImport implements ToModel,WithChunkReading,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::channel('DotRLGLImport')->info('Job created For Data: ',['row' => json_encode($row)]);
        JobProcessRLGLImport::dispatch($row);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}