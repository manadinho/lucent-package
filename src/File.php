<?php

namespace Manadinho\Lucent;

use SplFileObject;

/**
 * Class File
 * @package Manadinho\Lucent\File
 * 
 * @author Muhammad Imran Israr (mimranisrar6@gmail.com)
 */
class File implements \Iterator
{
    /** @var \SplFileObject */
    private $file;

    /** @var int */
    private $lineNumber = 0;

    public function __construct(string $path)
    {
        $this->file = new SplFileObject($path);
    }

    /**
     * Get the number of lines in the file.
     *
     * This method will move the file pointer to the end of the file, count the lines,
     * and then return the total number of lines in the file.
     *
     * @return int The number of lines in the file.
     */
    public function numberOfLines(): int
    {
        $this->seekEnd();

        return $this->file->key() + 1;
    }

    /**
     * Get a specific line from the file or the next line if no line number is provided.
     *
     * @param int|null $lineNumber The line number to retrieve. If null, the next line will be returned.
     *
     * @return string The content of the specified line or the next line in the file.
     */
    public function getLine(int $lineNumber = null): string
    {
        if (is_null($lineNumber)) {
            return $this->getNextLine();
        }

        $this->seekLine($lineNumber);

        return $this->file->current();
    }

    /**
     * Get the next line from the file and update the line number.
     *
     * @return string The next line from the file.
     */
    public function getNextLine(): string
    {
        $this->file->next();
        $this->lineNumber++;

        return $this->file->current();
    }

    /**
     * Move the file pointer to the end of the file.
     *
     * This method seeks the file pointer to the end of the file, allowing
     * reading or writing operations to occur at the end of the file.
     *
     * @throws \RuntimeException If the seek operation fails.
     */
    private function seekEnd(): void
    {
        $this->file->seek(PHP_INT_MAX);
    }

    /**
     * Seeks to a specific line number in the file.
     *
     * @param int $lineNumber The line number to seek to. Must be greater than or equal to 1.
     *
     * @throws \InvalidArgumentException If the line number is less than 1.
     *
     * @return void
     */
    private function seekLine(int $lineNumber): void
    {
        if ($lineNumber < 1) {
            throw new \InvalidArgumentException("Line number must be greater than or equal to 1.");
        }

        if ($lineNumber !== $this->lineNumber) {
            $this->file->seek($lineNumber - 1);
            $this->lineNumber = $lineNumber;
        }
    }

    /**
     * Rewind the file pointer to the beginning of the file and reset the line number counter.
     *
     * This method resets the file pointer to the start of the file and sets the line number
     * counter to zero, allowing you to read the file from the beginning.
     *
     * @return void
     */
    public function rewind()
    {
        $this->file->rewind();
        $this->lineNumber = 0;
    }

    /**
     * Get the current item from the file iterator.
     *
     * @return mixed|null The current item, or null if the iterator is empty.
     */
    public function current()
    {
        return $this->file->current();
    }

    /**
     * Returns the current key (line number).
     *
     * @return int The current key (line number).
     */
    public function key()
    {
        return $this->lineNumber;
    }

    /**
     * Move to the next item in the file and increment the line number.
     *
     * This function moves the pointer to the next item in the file and increments
     * the line number count to keep track of the current position in the file.
     *
     * @return void
     */
    public function next(): void
    {
        $this->file->next();
        $this->lineNumber++;
    }

    /**
     * Check if the current position in the file is valid.
     *
     * @return bool Returns `true` if the current position in the file is valid and `false` if it is not.
     */
    public function valid(): bool
    {
        return !$this->file->eof();
    }
}
