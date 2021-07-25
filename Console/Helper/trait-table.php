<?php
/**
 * Interface for defining methods in command classes.
 */
namespace Themalizer\Console\Helper;

if ( ! defined( 'THEMALIZER_CLI' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}


use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Trait to write out tables
 */
trait Table {

	protected function table_checker() {
		$condition1 = ( isset( $this->interact_state ) || isset( $this->excute_state ) ) && isset( $this->output );
		$condition2 = $condition1 ? $this->output instanceof \Symfony\Component\Console\Output\OutputInterface : false;
		if ( ! $condition1 ) {
			$current_class = get_class();
			throw new \Exception( "Table trait should not be used in a class out of scoop 'Themalizer\Console\Core' class type, this instance is a type of '$current_class'!" );
		} elseif ( ! $condition2 ) {
			throw new \Exception( "Output property should be an instance of 'Symfony\Component\Console\Output\OutputInterface'" );
		}

		if ( ! $this->interact_state && ! $this->excute_state ) {
			throw new \Exception( 'You can\'t create the table as it is being called outside of the Command lifecycle!' );
		}
	}

	protected function generate_table( $headers, $rows, $seperator = true ) {
		$this->table_checker();

		if ( ! is_array( $headers ) && ! is_array( $rows ) ) {
			throw new \Exception( 'The Header and the Rows should be of type Array()' );
		}

		$table_rows = array();

		foreach ( $rows as $value ) {
			array_push( $table_rows, $value );
			if ( $seperator ) {
				array_push( $table_rows, end( $rows ) !== $value ? ( new TableSeparator() ) : [] );
			}
		}
		
		$table = new SymfonyTable( $this->output );
		$table
		->setHeaders( $headers )
		->setRows( $table_rows );
		// $table->setStyle( 'box' );
		$table->render();
	}

}
