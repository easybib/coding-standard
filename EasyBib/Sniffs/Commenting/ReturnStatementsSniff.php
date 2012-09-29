<?php
namespace EasyBib\Sniffs\Commenting;

class ReturnStatementsSniff implements \PHP_CodeSniffer_Sniff
{
    /**
     * @var \PHP_CodeSniffer_File
     */
    protected $phpcsFile;

    /**
     * @var array
     */
    protected $tokens;

    public function register()
    {
        return array(T_FUNCTION);
    }

    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->phpcsFile = $phpcsFile;

        $methodName = $this->phpcsFile->getDeclarationName($stackPtr);
        if (null === $methodName) {
            return;
        }

        $this->tokens = $phpcsFile->getTokens();

        // enforce no `@return void` on __construct
        if ('__construct' === $methodName) {
            $this->findReturnVoid($stackPtr, true);
            return;
        }
    }

    /**
     * @param int  $stackPtr Position in the stack.
     * @param bool $ctor     Is this a __construct.
     *
     * @return void
     */
    protected function findReturnVoid($stackPtr, $ctor = false)
    {
        $toFind     = array(T_DOC_COMMENT,);
        $commentEnd = $this->phpcsFile->findPrevious(T_DOC_COMMENT, ($stackPtr-1));
        if (false === $commentEnd) {
            return; // no docblock, different concern
        }

        $look = $commentEnd-1;
        while (true && ($look > 0)) {
            $docComment = $this->phpcsFile->findPrevious(T_DOC_COMMENT, ($look));
            if (false === $docComment) {
                return;
            }
            $docRow  = $this->tokens[$docComment];
            $content = trim($docRow['content']);
            if (strstr($content, '@return void')) {
                if (true === $ctor) {
                   $this->phpcsFile->addError('`@return void` for __construct is invalid.', $docComment, 'ConstructNoReturnVoid');
                } else {
                    $this->phpcsFile->addWarning('Consider adding a fluent interface instead of `@return void`', $docComment, 'AddSetReturnsVoid');
                }
                return;
            }
            if ('/**' == $content) {
                return;
            }
        }
        return;
    }
}
