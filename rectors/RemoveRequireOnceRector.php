<?php


namespace Aliyun\Log\Rectors;


use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RemoveRequireOnceRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        // TODO: Implement getRuleDefinition() method.
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\Include_::class];
    }

    public function refactor(Node $node)
    {
        return $this->removeNode($node);
    }

}
