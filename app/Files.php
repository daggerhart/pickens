<?php

namespace Pickens;

use Symfony\Component\Finder\Finder;
//use Symfony\Component\Filesystem\Filesystem;

class Files {
	protected $realRoot;
	protected $finder;

	/**
	 * Symfony\Component\Finder\SplFileInfo
	 *
	 * @var
	 */
	public $root;

	// options from config file
	public $system = [];

	function __construct( $system ){
		$this->system = $system;

		$this->realRoot = realpath( dirname( __DIR__ ) . '/' . ltrim( $system['root'], '/' ) );

		$finder = $this->newFinder();
		$finder->directories()
			->in( dirname( $this->realRoot ) )
			->name( basename( $this->realRoot ) );

		foreach( $finder as $dir ){
			$this->root = $dir;
			break;
		}
	}

	/**
	 * @return Finder
	 */
	function newFinder(){
		$finder = new Finder();
		$finder->depth('== 0');
		$finder->followLinks( true );
		$finder->ignoreVCS( $this->system['ignoreVCS'] );
		$finder->ignoreDotFiles( $this->system['ignoreDotFiles'] );
		$finder->path( $this->realRoot );

		if ( $this->root ) {
			$finder->ignoreUnreadableDirs()->in( $this->root->getRealPath() );
		}

		foreach( $this->system['ignore'] as $ignore ){
			$finder->notName( $ignore );
		}

		return $finder;
	}

	/**
	 * @param $filePath
	 *
	 * @return string
	 */
	function getSystemRelativePath( $filePath ){
		$relative = str_replace( $this->realRoot, '', $filePath );

		return $relative;
	}

	/**
	 * @param $filePath
	 *
	 * @return string
	 */
	function getSystemAbsolutePath( $filePath = '' ){
		$relative = $this->getSystemRelativePath( $filePath );
		$absolute = $this->realRoot . '/' . ltrim( $relative, '/' );

		return $absolute;
	}

	/**
	 * @param $folder
	 *
	 * @return array
	 */
	function getFiles( $folder ) {
//		$finder = $this->newFinder();
//
//		d($folder);
//		d($finder);

		// @todo - why does path() not work here?
		$finder = new Finder();
		$finder->files()->in( $this->getSystemAbsolutePath( $folder ) );

		$files = [];
		foreach ( $finder as $file ) {
			$files[ $file->getRelativePath() ] = $this->makeFileArray( $file );
		}
		return $files;
	}

	/**
	 * @return array
	 */
	function getRootFolders(){
		$finder = $this->newFinder();
		$finder->directories()->in( '.' );

		foreach( $this->system['folders'] as $folder ){
			$finder->name( $folder );
		}

		$files = [];
		foreach( $finder as $dir ){
			$fileData = $this->makeFileArray( $dir );
			$files[ $dir->getRelativePath() ] = $fileData;
		}
		return $files;
	}

	/**
	 * @param $file
	 *
	 * @return array
	 */
	function makeFileArray( $file ){
		$fileData = [
			'relativePath' => $file->getRelativePathname(),
			'relativeDir'  => dirname( $file->getRelativePathname() ),
			'fileName'     => $file->getBasename(),
			'absPath'      => $file->getRealPath(),
			'isDir'        => $file->isDir(),
			'isFile'       => $file->isFile(),
			'isLink'       => $file->isLink(),
			'modified'     => $file->getMTime(),
		];

		if ( $file->isFile() ) {
			$fileData['mimeType'] = Utils::mimeType( $file->getRealPath() );
			$fileData['fileSize'] = $file->getSize();
		}

		if ( $file->isLink() ) {
			$fileData['linkTarget'] = $file->getLinkTarget();
		}

		return $fileData;
	}

	/**
	 * @param $folder
	 *
	 * @return array
	 */
	function getFolderContents( $folder ){
		$absolute = $this->getSystemAbsolutePath( $folder );
		$finder = $this->newFinder();
		$files = [];

		// dirs
		$finder->directories()
			->in( $absolute );

		foreach( $finder as $dir ){
			$relativePath = $this->getSystemRelativePath( $dir->getRealPath() );
			$files[ $relativePath ] = $this->makeFileArray( $dir, $relativePath );
		}

		// files
		$finder = $this->newFinder();
		$finder->files()
			->in( $folder );

		foreach( $finder as $file ) {
			$fileData = $this->makeFileArray( $file );

			$files[ $file->getRelativePath() ] = $fileData;
		}

		return $files;
	}


	/**
	 * @param $relativePath
	 *
	 * @return array
	 */
	function getFile( $relativePath ){
		$absolute = $this->getSystemAbsolutePath( $relativePath );
		if ( file_exists( $absolute ) ){
			$finder = new Finder();
			$finder->files()
				->in( dirname( $absolute ) )
				->name( basename( $relativePath ) );

			$found = null;

			foreach( $finder as $file ){
				$found = $this->makeFileArray( $file );
				break;
			}

			return $found;
		}

		return [];
	}

	/**
	 * @param $relativePath
	 * @param $newContent
	 */
	function updateFileContents( $relativePath, $newContent ){
		$filePath = $this->getSystemAbsolutePath( $relativePath );


		if ( file_exists( $filePath ) ){
			file_put_contents( $filePath, $newContent );
		}
	}

}