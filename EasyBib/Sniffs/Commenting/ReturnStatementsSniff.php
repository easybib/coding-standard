<?php

class EasyBib_Sniffs_Commenting_ReturnStatementsSniff implements \PHP_CodeSniffer_Sniff
{
    public function register()
    {
        return array(T_FUNCTION);
    }

    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if (null === $methodName) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        if ('__construct' === $methodName) {
            $toFind     = array(T_DOC_COMMENT,);
            $commentEnd = $phpcsFile->findPrevious(T_DOC_COMMENT, ($stackPtr-1));
            if (false === $commentEnd) {
                return;
            }
            $look = $commentEnd-1;
            while (true && ($look > 0)) {
                $docComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($look));
                if (false === $docComment) {
                    return;
                }
                $docRow  = $tokens[$docComment];
                $content = trim($docRow['content']);
                if (strstr($content, '@return void')) {
                    $phpcsFile->addError('`@return void` for __construct is invalid.', $docComment, 'ConstructNoReturnVoid');
                    return;
                }
                if ('/**' == $content) {
                    return;
                }
            }
        }
    }
}
