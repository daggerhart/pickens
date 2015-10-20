<?php

namespace Pickens;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Yaml;

class Settings {
	protected $options = [];

	function __construct( $configFile ){
		$options = Yaml::Parse( $configFile );

		$resolver = new OptionsResolver();
		$this->configureOptions($resolver);

		$this->options = $resolver->resolve( $options );
	}

	function configureOptions( $resolver ){
		$resolver->setDefaults([
			'filesystems' => [
				'local' => [
					'root' => '../',
					'folders' => [],
					'ignore' => [],
					'ignoreVCS' => true,
					'ignoreDotFiles' => true,
				]
			],
		]);

		$resolver->setRequired( 'filesystems' );
	}

	function values(){
		return $this->options;
	}
}

