<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace EasyCI20220520\Nette\Neon\Node;

use EasyCI20220520\Nette\Neon\Entity;
use EasyCI20220520\Nette\Neon\Node;
/** @internal */
final class EntityNode extends \EasyCI20220520\Nette\Neon\Node
{
    /** @var Node */
    public $value;
    /** @var ArrayItemNode[] */
    public $attributes;
    public function __construct(\EasyCI20220520\Nette\Neon\Node $value, array $attributes = [])
    {
        $this->value = $value;
        $this->attributes = $attributes;
    }
    public function toValue() : \EasyCI20220520\Nette\Neon\Entity
    {
        return new \EasyCI20220520\Nette\Neon\Entity($this->value->toValue(), \EasyCI20220520\Nette\Neon\Node\ArrayItemNode::itemsToArray($this->attributes));
    }
    public function toString() : string
    {
        return $this->value->toString() . '(' . ($this->attributes ? \EasyCI20220520\Nette\Neon\Node\ArrayItemNode::itemsToInlineString($this->attributes) : '') . ')';
    }
    public function &getIterator() : \Generator
    {
        (yield $this->value);
        foreach ($this->attributes as &$item) {
            (yield $item);
        }
    }
}
