<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Image;

/**
 * Method uploadFile
 *
 * @param UploadedFile $file             [explicite description]
 * @param string       $folder           [explicite description]
 * @param string       $permission       [explicite description]
 * @param array        $thumbnailOptions [explicite description]
 *
 * @return void
 */
function uploadFile(
    UploadedFile $file,
    string $folder,
    string $permission = 'public',
    array $thumbnailOptions = []
) {
    $extention = getFileExtention($file);
    $filenameWithoutExt = getFileName($file) . time();
    $filenameWithoutExt = str_replace('.', '_', $filenameWithoutExt);
    $filename = $filenameWithoutExt . '.' . $extention;
    $file_path = $folder . '/' . $filename;
    $filesystem = config('filesystems.default');
    $key = '';
    if ('private' == $permission) {
        $key = Storage::disk(config('filesystems.disk.private'))
            ->putFileAs($folder, $file, $filename);
    } elseif (! Storage::exists($file_path)) {
        $key = Storage::putFileAs($folder, $file, $filename);
    }

    if ($thumbnailOptions && 'image' == getFileType($extention)) {
        $thumbnailPath = $folder . '/thumb/' . $filename;
        if (! Storage::exists($thumbnailPath)) {
            $thumb = createThumbnail(
                $file,
                $thumbnailOptions['width'],
                $thumbnailOptions['height']
            );
            Storage::put($thumbnailPath, $thumb);
        }
    }

    if ($thumbnailOptions
        && 'video' == getFileType($extention)
        && 'local' == $filesystem
    ) {
        $thumbnailPath = $folder . '/thumb/' . $filenameWithoutExt . '.jpeg';
        if (! Storage::exists($thumbnailPath)) {
            $thumb = createThumbnailFromVideo(
                $key,
                $thumbnailOptions['width'],
                $thumbnailOptions['height']
            );
            Storage::put($thumbnailPath, $thumb);
        }
    }

    return $key;
}

/**
 * Method getImageUrl
 *
 * @param $fileName   $fileName [explicite description]
 * @param string $permission [explicite description]
 *
 * @return string
 */
function getImageUrl($fileName, string $permission = 'public'): string
{
    $src = getNoImageUrl();

    if ($fileName) {
        if ('private' == $permission) {
            $exists = Storage::disk(config('filesystems.private'))
                ->exists($fileName);
            if ($exists && $fileName) {
                $filesystem = config('filesystems.default');
                if (in_array($filesystem, ['local', 'public'])) {
                    $src = Storage::url($fileName);
                } else {
                    $src = Storage::disk(\config('filesystems.private'))
                        ->temporaryUrl($fileName, now()->addSecond(3));
                }
            }
        } else {
            $exists = Storage::exists($fileName);
            if ($exists && $fileName) {
                $src = Storage::url($fileName);
            }
        }
    }

    return $src;
}

/**
 * Method getThumbnailUrl
 *
 * @param string $fileName [explicite description]
 * @param $type     $type [explicite description]
 *
 * @return void
 */
function getThumbnailUrl(string $fileName = '', $type = '')
{
    $src = getNoImageUrl();
    if ($fileName) {
        if (in_array($type, ['video', 'image'])) {
            $thumbPath = getThumbPath($fileName);
            $exists = Storage::exists($thumbPath);
            if ($exists) {
                $src = Storage::url($thumbPath);
            }
        } else {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if ('pdf' == $ext) {
                return asset('assets/images/pdf-thumb.jpg');
            }

            return asset('assets/images/document-thumb.png');

        }
    }

    return $src;
}

/**
 * Method deleteFile
 *
 * @param string|null $filePath [explicite description]
 *
 * @return bool
 */
function deleteFile($filePath): bool
{
    $exists = Storage::exists($filePath);
    if ($exists) {
        Storage::delete($filePath);
        $thumb = getThumbPath($filePath);
        if ($thumb && Storage::exists($thumb)) {
            Storage::delete($thumb);
        }

        return true;
    }

    return false;
}

/**
 * Method getThumbPath
 *
 * @param string $fileKey [explicite description]
 *
 * @return string
 */
function getThumbPath(string $fileKey): string
{
    $parts = explode('/', $fileKey);
    $mediaName = array_pop($parts);
    $nameParts = explode('.', $mediaName);
    $mediaExt = array_pop($nameParts);
    $thumbPath = implode('/', $parts);
    if ('video' == getFileType($mediaExt)) {
        $thumbPath .= '/thumb/' . $nameParts[0] . '.jpeg';
    } elseif ('image' == getFileType($mediaExt)) {
        $thumbPath .= '/thumb/' . $mediaName;
    }

    return $thumbPath;
}

/**
 * Method getFileName
 *
 * @param $file $file [explicite description]
 *
 * @return string
 */
function getFileName($file): string
{
    return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
}

/**
 * Method getFileExtention
 *
 * @param $file $file [explicite description]
 *
 * @return string
 */
function getFileExtention($file): string
{
    return pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
}

/**
 * Method createThumbnail
 *
 * @param $path   $path [explicite description]
 * @param $width  $width [explicite description]
 * @param $height $height [explicite description]
 *
 * @return void
 */
function createThumbnail($path, $width, $height)
{
    $image = Image::make($path)
        ->resize(
            $width,
            $height,
            function ($constraint): void {
                $constraint->aspectRatio();
            }
        );

    return $image->stream();
}

/**
 * Method createThumbnailFromVideo
 *
 * @param string $video  [explicite description]
 * @param int    $width  [explicite description]
 * @param int    $height [explicite description]
 *
 * @return void
 */
function createThumbnailFromVideo(string $video, $width, $height)
{
    $filesystem = config('filesystems.default');
    $contents = FFMpeg::fromDisk($filesystem)
        ->open($video)
        ->getFrameFromSeconds(2)
        ->export()
        ->getFrameContents();

    return createThumbnail($contents, $width, $height);
}

/**
 * Method getFileType
 *
 * @param $ext $ext [explicite description]
 *
 * @return string
 */
function getFileType($ext): string
{
    $fileType = '';
    $image = ['png', 'jpg', 'jpeg', 'svg'];
    $video = ['mp4', 'ogx', 'oga', 'ogv', 'ogg', 'webm', '3gp', 'mov'];
    $pdf = ['pdf'];
    $doc = ['doc', 'docx'];
    switch ($ext) {
    case in_array($ext, $image):
            $fileType = 'image';
        break;
    case in_array($ext, $video):
            $fileType = 'video';
        break;
    case in_array($ext, $pdf):
            $fileType = 'pdf';
        break;
    case in_array($ext, $doc):
            $fileType = 'doc';
        break;
    default:
            $fileType = 'default';
        break;
    }

    return $fileType;
}

/**
 * Method getExtensionFromKey
 *
 * @param string $key [explicite description]
 *
 * @return string
 */
function getExtensionFromKey(string $key): string
{
    return pathinfo($key, PATHINFO_EXTENSION);
}

/**
 * Method getFileTypeFromMime
 *
 * @param string $mimeType [explicite description]
 *
 * @return string
 */
function getFileTypeFromMime(string $mimeType)
{
    $fileType = '';
    $image = [
        'image/png', 'image/jpeg', 'image/gif', 'image/svg+xml',
    ];

    $pdf = [
        'application/pdf',
    ];

    $doc = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    $video = [
        'video/mp4', 'video/mpeg', 'video/ogg', 'video/mp2t',
        'video/webm', 'video/3gpp', 'video/3gpp2',
    ];

    switch ($mimeType) {
    case in_array($mimeType, $image):
        $fileType = 'image';
        break;
    case in_array($mimeType, $video):
        $fileType = 'video';
        break;
    case in_array($mimeType, $pdf):
        $fileType = 'pdf';
        break;
    case in_array($mimeType, $doc):
        $fileType = 'doc';
        break;
    default:
        $fileType = 'text';
        break;
    }

    return $fileType;
}

/**
 * Method saveFacebookAvatar
 *
 * @param ?string $url       [explicite description]
 * @param string  $folder    [explicite description]
 * @param string  $imageName [explicite description]
 *
 * @return void
 */
function saveFacebookAvatar(
    ?string $url,
    string $folder,
    string $imageName
) {
    $image = file_get_contents($url);
    $imageName = $folder . '/' . $imageName;
    Storage::put($imageName, $image);

    return $imageName;
}

/**
 * Method getAssetImage
 *
 * @param $filename   $filename [explicite description]
 * @param $type       $type    [explicite description]
 * @param string $permission [explicite description]
 *
 * @return void
 */
function getAssetImage(
    $filename,
    $type = 'assets',
    string $permission = 'public'
) {
    $src = getNoImageUrl();
    if ('' != $filename) {
        if ('assets' == $type) {
            $src = url('assets/images/' . $filename);
        } else {
            $src = getImageUrl($filename, $permission);
        }
    }

    return $src;
}

/**
 * Method getNoImageUrl
 *
 * @return string
 */
function getNoImageUrl()
{
    return url(config('constants.image.defaultNoImage'));
}
