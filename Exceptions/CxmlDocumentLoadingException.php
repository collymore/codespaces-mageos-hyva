<?php
    
namespace Develodesign\Punchout\Exceptions;

use LibXMLError;

class CxmlDocumentLoadingException extends \Exception
{
    /** @var LibXMLError[] */
    private $libxmlErrors;
    
    /**
     * CxmlDocumentLoadingException constructor.
     *
     * @param LibXMLError[] $libxmlErrors
     */
    public function __construct(array $libxmlErrors)
    {
        $this->libxmlErrors = $libxmlErrors;
        $first              = $this->libxmlErrors[0];
        
        parent::__construct(
            \sprintf(
                '%s (Line: %d / Column: %d / File: %s)',
                $first->message,
                $first->line,
                $first->column,
                $first->file
            ),
            $first->code
        );
    }
    
    /**
     * @return LibXMLError[]
     */
    public function getLibxmlErrors(): array
    {
        return $this->libxmlErrors;
    }
}
