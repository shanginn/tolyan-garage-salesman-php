<?php

declare(strict_types=1);

namespace Tolyan\Bot;

use Exception;
use HttpException;
use Throwable;

class MultipartFormData
{
    /** @var FormField[] */
    private array $fields = [];

    private string $boundary;

    private string $body;

    private int $size;

    public function __construct(
        array $fields = [],
        ?string $boundary = null
    ) {
        try {
            $this->boundary = $boundary ?? \bin2hex(\random_bytes(16));
        } catch (Exception $exception) {
            throw new HttpException('Failed to obtain random boundary', 0, $exception);
        }

        foreach ($fields as $name => $field) {
            if ($field instanceof FormField) {
                $this->fields[$name] = $field;
            } elseif (is_file($field)) {
                $this->addFile($name, $field);
            } else {
                $this->addField($name, $field);
            }
        }
    }

    public function addField(
        string $name,
        string $content,
        ?int $contentLength = null,
        ?string $contentType = null,
        ?string $filename = null,
    ): void {
        if (isset($this->body)) {
            unset($this->body);
        }

        if (isset($this->size)) {
            unset($this->size);
        }

        $this->fields[$name] = new FormField(
            content: $content,
            contentLength: $contentLength,
            contentType: $contentType,
            filename: $filename,
        );
    }

    public function addFile(string $name, string $path, ?string $contentType = null): void
    {
        if (!\is_file($path)) {
            throw new HttpException("File not found: {$path}");
        }

        $file = \fopen($path, 'r');
        if ($file === false) {
            throw new HttpException("Failed to open file: {$path}");
        }

        try {
            $info        = \fstat($file);
            $content     = \fread($file, $info['size']);
            $contentType = $contentType ?? (\mime_content_type($file) ?: null) ?? 'application/octet-stream';
        } catch (Throwable $exception) {
            throw new HttpException("Failed to read file: {$path}", 0, $exception);
        } finally {
            \fclose($file);
        }

        $this->addField(
            name: $name,
            content: $content,
            contentLength: $info['size'],
            contentType: $contentType,
            filename: \basename($path),
        );
    }

    public function getBody(): string
    {
        if (!isset($this->body)) {
            $body = '';

            foreach ($this->fields as $name => $field) {
                $body .= <<<BODY
                    --{$this->boundary}\r
                    Content-Disposition: form-data; name="{$name}"{$field->bodyContent}\r

                    BODY;
            }

            $body .= "--{$this->boundary}--\r\n";

            $this->body = $body;
        }

        return $this->body;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type'   => "multipart/form-data; boundary={$this->boundary}",
            'Content-Length' => $this->getSize(),
        ];
    }

    private function getSize(): int
    {
        if (!isset($this->size)) {
            $this->size = \strlen($this->getBody());
        }

        return $this->size;
    }
}