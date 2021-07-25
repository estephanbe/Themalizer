<?php
/**
 * Command Part Abstract Class File
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

abstract class CommandPart {

	protected $command;

	protected $configure_state = false;

	protected $interact_state = false;

	protected $execute_state = false;

	protected $input;

	protected $output;

	public final function __construct($command_class) {
        $this->class_checker($command_class);
        $this->command = $command_class;
        $this->input = $this->command->input;
        $this->output = $this->command->output;
        $this->configure_state = $this->command->configure_state;
        $this->interact_state = $this->command->interact_state;
        $this->execute_state = $this->command->execute_state;
		return $this->init();
	}

    protected abstract function init();

    protected final function class_checker($class) {
		if ( ! is_subclass_of( $class, 'Themalizer\Console\Core' ) ) {
			$current_class = get_class();
			throw new \Exception( "This class should be used in classes with 'Themalizer\Console\Core' type, but this instance is a type of '$current_class'!" );
		}
	}

	
}
