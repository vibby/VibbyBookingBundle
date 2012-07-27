<?php
 
/*
 * This file is a Twig tag extension.
 *
 * (c) 2012 vibby
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loops over a time sequence.
 *
 * <pre>
 * <ul>
 *  {% fortime date from date1 to date2 step dateDec %}
 *    <li>{{ date|date('d/m/Y') }}</li>
 *  {% endfor %}
 * </ul>
 * </pre>
 * 
 * date1 and date2 must be DateTime objects
 * dateDec must be DateInterval object
 */
class Twig_TokenParser_Fortime extends \Twig_TokenParser
{
      /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
/*      
        $lineno = $token->getLine();
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $this->parser->getStream()->expect(Twig_Token::OPERATOR_TYPE, 'in');
        $seq = $this->parser->getExpressionParser()->parseExpression();

        $ifexpr = null;
        if ($this->parser->getStream()->test(Twig_Token::NAME_TYPE, 'if')) {
            $this->parser->getStream()->next();
            $ifexpr = $this->parser->getExpressionParser()->parseExpression();
        }

        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForFork'));
        if ($this->parser->getStream()->next()->getValue() == 'else') {
            $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse(array($this, 'decideForEnd'), true);
        } else {
            $else = null;
        }
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        if (count($targets) > 1) {
            $keyTarget = $targets->getNode(0);
            $keyTarget = new Twig_Node_Expression_AssignName($keyTarget->getAttribute('name'), $keyTarget->getLine());
            $valueTarget = $targets->getNode(1);
            $valueTarget = new Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());
        } else {
            $keyTarget = new Twig_Node_Expression_AssignName('_key', $lineno);
            $valueTarget = $targets->getNode(0);
            $valueTarget = new Twig_Node_Expression_AssignName($valueTarget->getAttribute('name'), $valueTarget->getLine());
        }

        return new Twig_Node_For($keyTarget, $valueTarget, $seq, $ifexpr, $body, $else, $lineno, $this->getTag());
        */
     
    }

    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endfortime');
    }
    
    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'fortime';
    }
}

