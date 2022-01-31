<?php

interface NodeInterface
{

    public function __construct(string $name);

    public function __toString(): string;
    
    public function getName(): string;

    /**
     * @return Node[]
     */
    public function getChildren(): array;

    public function addChild(Node $node): self;

    public function getNodeIndex(): int;

    public function setNodeIndex(int $nodeIndex);

    
}
