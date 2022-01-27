<?php

declare (strict_types=1);
namespace EasyCI20220127\Symplify\Astral\ValueObject\NodeBuilder;

use EasyCI20220127\PhpParser\Builder\Use_;
use EasyCI20220127\PhpParser\Node\Stmt\Use_ as UseStmt;
/**
 * @api
 * Fixed duplicated naming in php-parser and prevents confusion
 */
final class UseBuilder extends \EasyCI20220127\PhpParser\Builder\Use_
{
    public function __construct($name, int $type = \EasyCI20220127\PhpParser\Node\Stmt\Use_::TYPE_NORMAL)
    {
        parent::__construct($name, $type);
    }
}
