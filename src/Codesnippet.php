<?php

namespace Manadinho\Lucent;

use Exception;

/**
 * Class Codesnippet
 * @package Manadinho\Lucent\Codesnippet
 * 
 * @author Muhammad Imran Israr (mimranisrar6@gmail.com)
 */
class Codesnippet
{
    /** @var int */
    private $surroundingLine = 1;

    /** @var int */
    private $snippetLineCount = 10;

    public function surroundingLine(int $surroundingLine): self
    {
        $this->surroundingLine = $surroundingLine;

        return $this;
    }

    /**
     * Get a portion of code from the specified file.
     *
     * @param string $fileName The path to the file.
     * @return array An array containing the code lines as values and their line numbers as keys.
     */
    public function get(string $fileName): array
    {
        if (!file_exists($fileName)) {
            return [];
        }

        try {
            $file = new File($fileName);

            [$currentLineNumber, $endLineNumber] = $this->getBounds($file->numberOfLines());

            $code = [];

            while ($currentLineNumber <= $endLineNumber) {
                $code[$currentLineNumber] = rtrim(substr($file->getLine($currentLineNumber), 0, 250));
                
                $currentLineNumber++;
            }

            return $code;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Calculate the start and end line numbers to extract from the file.
     *
     * @param int $totalNumberOfLineInFile The total number of lines in the file.
     *
     * @return array An array containing the start and end line numbers [startLine, endLine].
     */
    private function getBounds($totalNumberOfLineInFile): array
    {
        $startLine = max($this->surroundingLine - (int)ceil($this->snippetLineCount / 2), 1);
        $endLine = min($startLine + $this->snippetLineCount - 1, $totalNumberOfLineInFile);

        return [$startLine, $endLine];
    }
}
