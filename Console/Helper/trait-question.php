<?php
/**
 * Trait for question
 */
namespace Themalizer\Console\Helper;

if ( ! defined( 'THEMALIZER_CLI' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

use Symfony\Component\Console\Question\ConfirmationQuestion; // Boolean.
use Symfony\Component\Console\Question\Question as SymfonyQuestion; // Text answer.
use Symfony\Component\Console\Question\ChoiceQuestion; // Multiple answer.

/**
 * Trait to interact with questions
 */
trait Question {

	use Formatter;

	protected $question_helper = null;

	protected function question_checker() {

		$condition1 = isset( $this->interact_state ) && isset( $this->input ) && isset( $this->output );
		$condition2 = $condition1 ? $this->input instanceof \Symfony\Component\Console\Input\ArgvInput : false;
		$condition3 = $condition1 ? $this->output instanceof \Symfony\Component\Console\Output\OutputInterface : false;
		if ( ! $condition1 ) {
			$current_class = get_class();
			throw new \Exception( "Question trait should not be used in a class out of scoop 'Themalizer\Console\Core' class type, this instance is a type of '$current_class'!" );
		} elseif ( ! $condition2 ) {
			throw new \Exception( "Input property should be an instance of 'Symfony\Component\Console\Input\InputInterface'" );
		} elseif ( ! $condition3 ) {
			throw new \Exception( "Output property should be an instance of 'Symfony\Component\Console\Output\OutputInterface'" );
		}

		if ( ! $this->interact_state ) {
			throw new \Exception( 'You can\'t use questions outside the interact command lifecycle!' );
		}

		if ( is_subclass_of( $this, 'Themalizer\Console\Core' ) ) {
			$this->question_helper = $this->getHelper( 'question' );
		} elseif ( is_subclass_of( $this, 'Themalizer\Console\CommandPart' ) ) {
			if ( isset( $this->command ) ) {
				$this->question_helper = $this->command->getHelper( 'question' );
			} else {
				throw new \Exception( 'Class was not instantiated yet!' );
			}
		} else {
			$current_class = get_class();
			throw new \Exception( "Question trait should be used in classes with 'Themalizer\Console\Core' or 'Themalizer\Console\CommandPart' types, but this instance is a type of '$current_class'!" );
		}
	}


	protected function ask_question( $q, $default = false, $list = false ) {
		$this->question_checker();
		$question        = ( new SymfonyQuestion( $q . ' ', $default ) );
		$question_result = $this->question_helper->ask( $this->input, $this->output, $question );

		while ( false === $question_result ) {
			$this->throw_error( 'You cannot leave it empty!' );
			$question_result = $this->question_helper->ask( $this->input, $this->output, $question );
		}

		if ($question_result === null) {
			$this->write_out('Nothing was entered!', 'comment');
		}

		if ( $list && ! empty( $question_result ) ) {
			$question_result = $this->process_list_answer( $question_result );
		}

		return $question_result;

	}

	protected function confirm( $q, $default = false ) {
		$this->question_checker();
		$question        = ( new ConfirmationQuestion( $q . ' Yes/No (Default is No): ', $default ) );
		$question_result = $this->question_helper->ask( $this->input, $this->output, $question );

		return $question_result;
	}

	protected function multiple_choice( $q, $choices = array() ) {
		$this->question_checker();
		if ( empty( $choices ) ) {
			throw new \Exception( 'Please provide the question choices!' );
		}

		$question        = ( new ChoiceQuestion(
			"$q (defaults to $choices[0])",
			$choices,
			0
		) );
		$question_result = $this->question_helper->ask( $this->input, $this->output, $question );

		return $question_result;

		// $question->setErrorMessage('Color %s is invalid.');
	}

	protected function process_list_answer( $value ) {
		$this->question_checker();
		$validated_list = array();

		if ( ! strpos( $value, ',' ) ) { // tag1, tag2
			return array( $value );
		}

		$items = explode( ',', $value );

		foreach ( $items as $item ) {
			$item = trim( $item );
			if ( ! empty( $item ) ) {
				array_push( $validated_list, $item );
			}
		}

		return $validated_list;
	}

}
