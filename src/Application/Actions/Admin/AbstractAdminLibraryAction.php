<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\Page\AbstractPageAction;
use App\Domain\Library\LibraryRepository;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

abstract class AbstractAdminLibraryAction extends AbstractPageAction
{
    protected LibraryRepository $libraryRepository;

    public function __construct(LoggerInterface $logger, Twig $twig, LibraryRepository $libraryRepository)
    {
        parent::__construct($logger, $twig);
        $this->libraryRepository = $libraryRepository;
    }

    protected function slugify(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = (string) preg_replace('/[^a-z0-9-]+/', '-', $normalized);

        return trim($normalized, '-');
    }

    /**
     * @return array{path?: string, mime_type?: string, size_bytes?: int, error?: string}
     */
    protected function storeBookPdf(UploadedFileInterface $file): array
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return ['error' => 'Não foi possível enviar o PDF. Tente novamente.'];
        }

        $size = (int) $file->getSize();
        if ($size <= 0 || $size > (50 * 1024 * 1024)) {
            return ['error' => 'O PDF deve ter no máximo 50MB.'];
        }

        $clientMimeType = strtolower((string) $file->getClientMediaType());
        $clientFilename = strtolower(trim((string) $file->getClientFilename()));
        $hasPdfExtension = $clientFilename !== '' && substr($clientFilename, -4) === '.pdf';
        $allowedMimeTypes = ['application/pdf', 'application/x-pdf'];

        if (!$hasPdfExtension && !in_array($clientMimeType, $allowedMimeTypes, true)) {
            return ['error' => 'Formato inválido. Envie um arquivo PDF.'];
        }

        $projectRoot = dirname(__DIR__, 4);
        $targetDirectory = $projectRoot . '/public/assets/docs/library';

        if (!is_dir($targetDirectory) && !@mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
            $this->logger->warning('Diretório de PDFs da biblioteca indisponível.', [
                'directory' => $targetDirectory,
            ]);

            return ['error' => 'Não foi possível preparar o diretório de PDFs no servidor.'];
        }

        if (!is_writable($targetDirectory)) {
            $this->logger->warning('Diretório de PDFs da biblioteca sem permissão de escrita.', [
                'directory' => $targetDirectory,
            ]);

            return ['error' => 'O servidor não possui permissão para salvar o PDF enviado.'];
        }

        try {
            $timestamp = date('YmdHis');
            $randomSuffix = bin2hex(random_bytes(4));
            $fileName = sprintf('book_%s_%s.pdf', $timestamp, $randomSuffix);
        } catch (\Throwable $exception) {
            $this->logger->error('Falha ao gerar nome de arquivo para PDF da biblioteca.', [
                'exception' => $exception,
            ]);

            return ['error' => 'Falha ao processar o PDF enviado.'];
        }

        $targetPath = $targetDirectory . '/' . $fileName;

        try {
            $file->moveTo($targetPath);
        } catch (\Throwable $exception) {
            $this->logger->error('Falha ao gravar PDF da biblioteca.', [
                'exception' => $exception,
                'target_path' => $targetPath,
            ]);

            return ['error' => 'Não foi possível salvar o PDF no servidor.'];
        }

        return [
            'path' => 'assets/docs/library/' . $fileName,
            'mime_type' => $clientMimeType !== '' ? $clientMimeType : 'application/pdf',
            'size_bytes' => $size,
        ];
    }

    protected function deleteStoredPdfIfManaged(?string $relativePath): void
    {
        $normalizedPath = ltrim(trim((string) $relativePath), '/');

        if ($normalizedPath === '' || !str_starts_with($normalizedPath, 'assets/docs/library/')) {
            return;
        }

        $projectRoot = dirname(__DIR__, 4);
        $absolutePath = $projectRoot . '/public/' . $normalizedPath;

        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
