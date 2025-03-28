<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileManager
{
	public function __construct(
		private string           $uploadsDirectory,
		private SluggerInterface $slugger
	) {
	}

	/**
	 * Upload a file and return the new filename
	 */
	public function uploadFile(UploadedFile $file, ?int $trickId = null): string
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$safeFilename = $this->slugger->slug($originalFilename);

		// Add date-time to filename
		$dateTime = new \DateTime();
		$formattedDate = $dateTime->format('Y-m-d-H-i-s');
		$newFilename = $safeFilename . '-' . $formattedDate . '.' . $file->guessExtension();

		// If trickId is provided, create a subdirectory for this trick
		$targetDirectory = $this->uploadsDirectory;
		if ($trickId !== null) {
			$targetDirectory .= '/' . $trickId;
			if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0777, true) && !is_dir($targetDirectory)) {
				throw new FileException(sprintf('Directory "%s" could not be created', $targetDirectory));
			}
		}

		try {
			$file->move($targetDirectory, $newFilename);
		} catch (FileException $e) {
			throw new FileException('Failed to upload file: ' . $e->getMessage());
		}

		// Return path relative to uploads directory (including trick subfolder if applicable)
		return ($trickId !== null ? $trickId . '/' : '') . $newFilename;
	}

	/**
	 * Replace an existing file with a new one and return the new filename
	 */
	public function replaceFile(UploadedFile $file, ?string $oldFilename = null, ?int $trickId = null): string
	{
		// Delete old file if exists
		if ($oldFilename) {
			$this->deleteFile($oldFilename);
		}

		// Upload the new file
		return $this->uploadFile($file, $trickId);
	}

	/**
	 * Delete a file
	 */
	public function deleteFile(string $filename): bool
	{
		$filePath = $this->uploadsDirectory . '/' . $filename;
		if (file_exists($filePath)) {
			return unlink($filePath);
		}

		return false;
	}

	/**
	 * Get full path to file
	 */
	public function getFilePath(string $filename): string
	{
		return $this->uploadsDirectory . '/' . $filename;
	}

	/**
	 * Check if file exists
	 */
	public function fileExists(string $filename): bool
	{
		return file_exists($this->getFilePath($filename));
	}
}
