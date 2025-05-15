<?php

namespace App\Traits;


use App\Models\OrderFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{

    public function uploadFile(object $file): string
    {
        if($file) {
            $fileNameParts = explode('.', $file->getClientOriginalName());
            $fileName = $fileNameParts[0].time() . '.' . $fileNameParts[1];
            $file->storeAs("files", $fileName, 'public');
        }
       
        return $fileName ?? "";
    }


   
    public function fileDelete(string $filePath): void
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
    }



    public function saveOrderFiles(int $orderId, array $files): void
    {
        $userId = Auth::id();
        $instanceId = Auth::user()->instanceId;
        $data = [];

        foreach ($files as $file) {
            $fileName = $this->uploadFile($file);

            $data[] = [
                "orderId" => $orderId,
                'userId' => $userId,
                'instanceId' => $instanceId,
                "file" => $fileName
            ];
        }

        if (!empty($data)) {
            OrderFile::insert($data);
        }
    }

}
