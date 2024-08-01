<?php
namespace App\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class S3StorageService
{
    /**
     * Salva um arquivo no S3 em uma pasta específica.
     *
     * @param string $disk
     * @param string $directory
     * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file
     * @return string|null Nome do arquivo salvo, ou null em caso de falha
     */
    public function saveFileToS3(string $disk, string $directory, $file)
    {
        try {
            // Verifica se a pasta existe; se não existir, cria
            if (!Storage::disk($disk)->exists($directory)) {
                Storage::disk($disk)->makeDirectory($directory);
            }

            $storedFileName = $file->getClientOriginalName();
            $path = Storage::disk($disk)->putFileAs($directory, $file, $storedFileName);

            Log::info('File stored in S3', ['directory' => $directory, 'storedFileName' => $storedFileName, 'path' => $path]);

            return $path;
        } catch (\Exception $e) {
            Log::error('Error saving file to S3', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtém a URL de um arquivo no S3.
     *
     * @param string $disk
     * @param string $path
     * @return string|null URL do arquivo, ou null se não encontrado
     */
    public function getFileUrlFromS3(string $disk, string $path, int $expiration = 120)
    {
        try {
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($expiration));
        } catch (\Exception $e) {
            Log::error('Error getting file URL from S3', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Obtém o conteúdo de um arquivo no S3 como base64.
     *
     * @param string $disk
     * @param string $directory
     * @param string $filename
     * @return string|null Conteúdo do arquivo em base64, ou null se não encontrado
     */
    public function getFileBase64FromS3(string $disk, string $directory, string $filename)
    {
        try {
            $path = $directory . '/' . $filename;
            $contents = Storage::disk($disk)->get($path);
            $base64 = base64_encode($contents);
            return $base64;
        } catch (\Exception $e) {
            Log::error('Error getting file base64 from S3', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Exclui um arquivo do S3.
     *
     * @param string $disk
     * @param string $directory
     * @param string $filename
     * @return bool true se a exclusão foi bem-sucedida, false caso contrário
     */
    public function deleteS3File(string $disk, string $directory, string $filename)
    {
        try {
            $path = $directory . '/' . $filename;
            return Storage::disk($disk)->delete($path);
        } catch (\Exception $e) {
            Log::error('Error deleting file from S3', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Exclui um diretório e todo o seu conteúdo do S3.
     *
     * @param string $disk
     * @param string $directory
     * @return bool true se a exclusão foi bem-sucedida, false caso contrário
     */
    public function deleteS3Directory(string $disk, string $directory)
    {
        try {
            return Storage::disk($disk)->deleteDirectory($directory);
        } catch (\Exception $e) {
            Log::error('Error deleting directory from S3', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Realiza o upload de um arquivo para o S3 e retorna o nome do arquivo gerado.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $disk
     * @param string $directory
     * @return string|false Nome do arquivo gerado no S3 ou false em caso de falha
     */
    public function uploadFile(UploadedFile $file, string $disk, string $directory)
    {
        try {
            $filename = $file->getClientOriginalName();
            $path = Storage::disk($disk)->putFileAs($directory, $file, $filename, 'private');
            return basename($path);
        } catch (\Exception $e) {
            Log::error('Error uploading file to S3', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Gera um nome de arquivo para armazenamento no S3 baseado em um array de elementos.
     *
     * @param array $elements
     * @return string|null
     */
    public function generateS3FileNameFromArray(array $elements)
    {
        if (count($elements) < 2) {
            return null;
        }

        $sanitizedElements = array_map(function ($element) {
            return preg_replace('/[^a-zA-Z0-9\-_.]/', '', $element);
        }, $elements);

        $extension = array_pop($sanitizedElements);
        $fileName = implode('-', $sanitizedElements) . '.' . $extension;

        return $fileName;
    }

    /**
     * Obtém o conteúdo de um arquivo no S3.
     *
     * @param string $disk
     * @param string $directory
     * @param string $filename
     * @return string|null Conteúdo do arquivo, ou null se não encontrado
     */
    public function getFileContent(string $disk, string $directory, string $filename)
    {
        try {
            $path = $directory . '/' . $filename;
            return Storage::disk($disk)->get($path);
        } catch (\Exception $e) {
            Log::error('Error getting file content from S3', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
