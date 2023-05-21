<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PresignedUpload\GetPresignedDownloadUrlRequst;
use App\Http\Requests\PresignedUpload\GetPresignedUploadUrlRequst;
use Aws\S3\S3Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PresignedUploadUrlController extends Controller
{
    /**
     * アップロード用の署名付きURLを返す
     *
     * @param GetPresignedUploadUrlRequst $request
     * @return JsonResponse
     */
    public function presignedUploadUrl(GetPresignedUploadUrlRequst $request): JsonResponse
    {
        $file_name = $request->validated()['file_name'];

        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => '2006-03-01',
            'signature_version' => 'v4',
        ]);
        $uuid = Str::uuid();
        $base_path = "temp/{$uuid}";
        $upload_path = "{$base_path}/{$file_name}";
        // フロント側ではPUT methodでアップロード
        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $upload_path
        ]);
        $s3_cmd_result = $s3Client->createPresignedRequest($cmd, '+2 minutes');
        return response()->json([
            'upload_path' => $upload_path,
            'presigned_url' => (string)$s3_cmd_result->getUri()
        ]);
    }

    /**
     * ダウンロード用の署名付きURLを返す
     *
     * @param GetPresignedDownloadUrlRequst $request
     * @return void
     */
    public function presignedDownloadUrl(GetPresignedDownloadUrlRequst $request)
    {
        $file_path = $request->validated()['file_path'];
        /** @var FilesystemManager $s3 */
        $s3 = Storage::disk('s3');
        $presigned_url = $s3->temporaryUrl($file_path, '+2 minutes');
        return response()->json(compact('presigned_url'));
        // $s3Client = new S3Client([
        //     'region' => config('filesystems.disks.s3.region'),
        //     'version' => '2006-03-01',
        //     'signature_version' => 'v4',
        // ]);
        // $cmd = $s3Client->getCommand('GetObject', [
        //     'Bucket' => config('filesystems.disks.s3.bucket'),
        //     'Key' => $file_path
        // ]);
        // $s3_cmd_result = $s3Client->createPresignedRequest($cmd, '+2 minutes');
        // return response()->json([
        //     'presigned_url' => (string)$s3_cmd_result->getUri()
        // ]);
    }
}
