<?php
/**
 * Core Class File
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
namespace Themalizer\Console;

if ( ! defined( 'THEMALIZER_CLI' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Core extends Command {

	public $configure_state = false;

	public $interact_state = false;

	public $execute_state = false;

	public $input;

	public $output;

	final public function __construct() {
		parent::__construct();
	}

	abstract protected function init();

	abstract protected function in_configure();

	abstract protected function in_interact();

	abstract protected function in_excute();

	protected function configure() {
		$this->configure_state = true;
		$this->in_configure();
	}

	protected function interact( InputInterface $input, OutputInterface $output ) {
		$this->interact_state = true;
		$this->input          = $input;
		$this->output         = $output;
		$this->in_interact();
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$this->execute_state = true;
		$this->input         = $input;
		$this->output        = $output;
		$this->in_excute();
		return Command::SUCCESS;
	}

	protected function check_state( $state ) {
		$test_value = null;
		switch ( $state ) {
			case 'configure':
				$test_value = $this->configure_state;
				break;
			case 'interact':
				$test_value = $this->interact_state;
				break;
			case 'excute':
				$test_value = $this->excute_state;
				break;
			default:
				$test_value = null;
				break;
		}

		if ( ! $test_value ) {
			throw new \Exception( 'The app life cycle was interrupted!' );
		}
	}

	protected function generate_table_seperator() {
		return new TableSeparator();
	}

	protected function generate_table() {
		return new Table( $this->interact_output );
	}
}
