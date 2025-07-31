<?php

namespace App\Services;

use App\Exceptions\FileUploadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class FileUploadService
{
    private string $disk = 'public';

    public function create(UploadedFile $uploadedFile, string $directory): string
    {
        try {
            return $this->isImage($uploadedFile)
                ? $this->resize($uploadedFile, $directory)
                : Storage::disk($this->disk)->putFile($directory, $uploadedFile);
        } catch (\Throwable $e) {
            throw new FileUploadException('Erreur lors du téléchargement du fichier : ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(?UploadedFile $uploadedFile, string $directory, string $afterFile): string
    {
        try {

            if (null === $uploadedFile) {
                return $afterFile;
            }

            if (Storage::disk($this->disk)->exists($afterFile)) {
                $this->delete($afterFile);
            }

            return $this->isImage($uploadedFile)
                ? $this->resize($uploadedFile, $directory)
                : Storage::disk($this->disk)->putFile($directory, $uploadedFile);
        } catch (\Throwable $e) {
            throw new FileUploadException('Erreur lors du modification du fichier : ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->delete($path);
        } catch (\Exception $e) {
            throw new FileUploadException('Erreur lors de la suppression du fichier : ' . $e->getMessage(), 0, $e);
        }
    }

    private function isImage(UploadedFile $file): bool
    {
        return in_array(strtolower($file->getClientOriginalExtension()), ['png', 'jpg']);
    }

    private function resize(UploadedFile $upload, string $directory): string
    {
        $image = Image::read($upload)->resize(640, 480);

        $path = $directory . DIRECTORY_SEPARATOR . Str::random(100) . '.' . $upload->getClientOriginalExtension();
        $content =  $image->encodeByExtension($upload->getClientOriginalExtension(), quality: 90);

        if (!Storage::disk($this->disk)->put($path, $content)) {
            throw new FileUploadException("Une problème est survenue lors de la rédimensionnement de l'image, veullez changer d'image puis réessayer.");
        }

        return $path;
    }


    public function setDisk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }
}
