<?php

namespace BobV\LatexBundle\Exception;

/**
 * Class LatexException
 * Simple \Exception extend for better error origin check
 */
class LatexParseException extends LatexException
{

  const START_FROM = -2;
  const END_AT = 2;

  public function __construct($texLocation, $pdfLatexResult, array $pdfLatexOutput = NULL)
  {

    $message = "Something went wrong during the execution of the pdflatex command, as it returned $pdfLatexResult. See the log file ( " . explode('.tex', $texLocation)[0] . ".log ) for all details.";

    if ($pdfLatexOutput !== NULL) {
      $filteredOutput = $this->findErrors($pdfLatexOutput);
      if (count($filteredOutput) > 0) {
        $message .= "\n\n\nBelow some more info is tried to extract from the error:\n";
        $message .= implode("\n", $filteredOutput);
      }
    }

    parent::__construct($message, $pdfLatexResult);
  }

  /**
   * Try to find usefull information on the error that has occurred
   *
   * @param array $errorOutput
   *
   * @return array Contains the filtered output which should only contain information about the errors
   */
  protected function findErrors(array $errorOutput)
  {
    $filteredErrors   = array();
    $filteredErrors[] = "\n\n----------------------------------------------------------------------\n\n";

    array_walk($errorOutput, function ($value, $key) use (&$errorOutput, &$filteredErrors) {

      // Find lines with an error
      if (preg_match_all('/error|missing|not found|undefined|too many|runaway|\$/ui', $value) > 0) {

        // Get the five lines surround the error
        for ($i = self::START_FROM; $i <= self::END_AT; $i++) {
          $filteredErrors[] = $errorOutput[$key + $i];
        }
        $filteredErrors[] = "\n\n----------------------------------------------------------------------\n\n";
      }

    });

    return $filteredErrors;
  }


} 