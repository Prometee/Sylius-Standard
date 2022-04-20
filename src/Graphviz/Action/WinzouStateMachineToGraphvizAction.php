<?php

declare(strict_types=1);

namespace App\Graphviz\Action;

use App\Graphviz\Dumper\DumperInterface;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

final class WinzouStateMachineToGraphvizAction
{
    /** @var array */
    public const AVAILABLE_MIME_TYPES = [
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
    ];

    private ?string $outputImage = null;

    private ?string $dotFile = null;

    private array $smConfig;

    private DumperInterface $dumper;

    public function __construct(array $smConfig, DumperInterface $dumper)
    {
        $this->smConfig = $smConfig;
        $this->dumper = $dumper;
    }

    /**
     * @throws Exception
     */
    public function __invoke(string $machineName, string $format, Request $request): Response
    {
        if (false === isset(self::AVAILABLE_MIME_TYPES[$format])) {
            throw new Exception(sprintf("Format '%s' is not supported", $format));
        }

        if (false === isset($this->smConfig[$machineName])) {
            throw new Exception(sprintf("State machine '%s' not found", $machineName));
        }

        // Temporary files.
        $tempnam = tempnam(sys_get_temp_dir(), 'dot_');
        Assert::notFalse($tempnam);
        $this->dotFile = $tempnam;
        $tempnam1 = tempnam(sys_get_temp_dir(), $format . '_');
        Assert::notFalse($tempnam1);
        $this->outputImage = $tempnam1;

        // Dump
        $dotContent = $this->dumper->dump($this->smConfig[$machineName], [], [
            'graph' => ['rankdir' => 'TB', 'splines' => 'ortho', 'packMode' => 'graph'],
            'node' => ['shape' => 'box', 'style' => 'rounded,bold', 'fontsize' => '14'],
            'edge' => [],
        ]);

        // Save dot file for input.
        file_put_contents($this->dotFile, $dotContent);

        // exec
        $process = new Process([
            'dot',
            '-T',
            $format,
            '-o',
            $this->outputImage,
            $this->dotFile,
        ]);

        try {
            $process->run();
        } catch (Exception $e) {
            throw new ProcessFailedException($process);
        }

        return new BinaryFileResponse(
            $this->outputImage,
            200,
            [
                'Content-Type' => self::AVAILABLE_MIME_TYPES[$format],
            ]
        );
    }

    public function __destruct()
    {
        // Remove temporary files.
        if (null !== $this->outputImage) {
            unlink($this->outputImage);
        }
        if (null !== $this->dotFile) {
            unlink($this->dotFile);
        }
    }
}
