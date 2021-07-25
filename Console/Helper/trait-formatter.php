<?php
/**
 * Trait for formatter helper
 */
namespace Themalizer\Console\Helper;

/**
 * Formatter trait for formating outputs
 */
trait Formatter {

	protected $formatter_helper = null;

	protected function formatter_checker() {

		$condition1 = ( isset( $this->interact_state ) || isset( $this->excute_state ) ) && isset( $this->output );
		$condition2 = $condition1 ? $this->output instanceof \Symfony\Component\Console\Output\OutputInterface : false;
		if ( ! $condition1 ) {
			$current_class = get_class();
			throw new \Exception( "Formatter trait should not be used in a class out of scoop 'Themalizer\Console\Core' class type, this instance is a type of '$current_class'!" );
		} elseif ( ! $condition2 ) {
			throw new \Exception( "Output property should be an instance of 'Symfony\Component\Console\Output\OutputInterface'" );
		}

		if ( ! $this->interact_state && ! $this->excute_state ) {
			throw new \Exception( 'You can\'t use formatter as it is being called outside of the Command lifecycle!' );
		}

		if ( is_subclass_of( $this, 'Themalizer\Console\Core' ) ) {
			$this->formatter_helper = $this->getHelper( 'formatter' );
		} elseif ( is_subclass_of( $this, 'Themalizer\Console\CommandPart' ) ) {
			if ( isset( $this->command ) ) {
				$this->formatter_helper = $this->command->getHelper( 'formatter' );
			} else {
				throw new \Exception( 'Class was not instantiated yet!' );
			}
		} else {
			$current_class = get_class();
			throw new \Exception( "Formatter trait should be used in classes with 'Themalizer\Console\Core' or 'Themalizer\Console\CommandPart' types, but this instance is a type of '$current_class'!" );
		}
	}

	protected function throw_error( $msg ) {
		$this->formatter_checker();
		$errorMessages  = array( 'Error!', $msg );
		$formattedBlock = $this->formatter_helper->formatBlock( $errorMessages, 'error' );
		$this->output->writeln( $formattedBlock );
	}

	protected function info_msg( $msg, $large = false ) {
		$this->formatter_checker();
		$this->output->writeln( '' );
		$infoMessages   = array( $msg );
		$formattedBlock = $this->formatter_helper->formatBlock( $infoMessages, 'bg=green;fg=black', $large );
		$this->output->writeln( $formattedBlock );
		$this->output->writeln( '' );
	}

	protected function write_out( $msg, string $style = '', $large = false ) {
		$this->formatter_checker();
		if ( $style === '' ) {
			$this->output->writeln( $msg );
		} else {
			$the_msg        = array( $msg );
			$formattedBlock = $this->formatter_helper->formatBlock( $the_msg, $style, $large );
			$this->output->writeln( $formattedBlock );
		}

	}

}
