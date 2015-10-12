<?php

namespace Pickens;

class FileSystem {
	protected $root;
	protected $realRoot;

	public $files = [];
	public $folders = [];
	public $ignore = [];

	function __construct( $root, $ignore = [] ){
		$this->root = $root;
		$this->realRoot = realpath( __DIR__ . '/../' . $root );
        $this->ignore = $ignore;
	}

	/**
	 * @param $folders
	 */
	function setFolders( $folders ){
		if ( ! is_array( $folders ) ){
			$folders = [ $folders ];
		}

		foreach( $folders as $folder ){
			$this->folders[] = $this->realRoot . '/' . ltrim( $folder, '/' );
		}
	}

	/**
	 * @param $folders
	 *
	 * @return array
	 */
	function getFiles( $folders ) {
		if ( empty( $this->files ) ) {
			$this->setFolders( $folders );
			$this->files = $this->getFolderContents( $this->folders );
		}

		return $this->files;
	}

	/**
	 * @param $relativePath
	 *
	 * @return array
	 */
	function getFile( $relativePath ){
		if ( file_exists( $this->realRoot . $relativePath ) ){
			$file = new \SplFileInfo( $this->realRoot . $relativePath );
			return $this->makeFileArray( $file, $relativePath );
		}

		return [];
	}

	/**
	 * @param $relativePath
	 * @param $newContent
	 */
	function updateFileContents( $relativePath, $newContent ){
		$filePath = $this->realRoot . $relativePath;


		if ( file_exists( $filePath ) ){
			file_put_contents( $filePath, $newContent );
		}
	}

	/**
	 * @param $file
	 * @param $relativePath
	 *
	 * @return array
	 */
	function makeFileArray( $file, $relativePath ){
		$content = [
			'relativePath' => $relativePath,
			'relativeDir'  => dirname( $relativePath ),
			'fileName'     => $file->getBasename(),
			'absPath'      => $file->getRealPath(),
			'isDir'        => $file->isDir(),
			'isFile'       => $file->isFile(),
			'isLink'       => $file->isLink(),
			'modified'     => $file->getMTime(),
		];

		if ( $file->isFile() ) {
			$content['mimeType'] = Utils::mimeType( $file->getRealPath() );
			$content['fileSize'] = $file->getSize();
		}

		if ( $file->isLink() ) {
			$content['linkTarget'] = $file->getLinkTarget();
		}

		return $content;
	}

	/**
	 * @param $folders
	 *
	 * @return array
	 */
	function getFolderContents( $folders ){
		$contents = [];

		foreach ( $folders as $folder ) {
			$dir_iterator = new \RecursiveDirectoryIterator( $folder );
			$iterator     = new \RecursiveIteratorIterator( $dir_iterator, \RecursiveIteratorIterator::SELF_FIRST );

			foreach ( $iterator as $name => $file ) {
				// don't access files outsite of root
				if ( stripos( $file->getRealPath(), $folder ) === FALSE ) {
					continue;
				}

				$relativePath = str_replace( $this->realRoot, '', $file->getRealPath() );

				// don't access the root directory itself
				if ( empty( $relativePath ) ) {
					continue;
				}

				// skip ignored files
				if ( !empty( $this->ignore ) && in_array( $file->getBasename(), $this->ignore ) ) {
					continue;
				}

				$content = $this->makeFileArray( $file, $relativePath );

				$contents[ $relativePath ] = $content;
			}
		}

		return $contents;
	}
}