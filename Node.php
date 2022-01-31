<?php

class Node implements NodeInterface
{
    private $node_name = "";
    private $node_index = null;
    private $childs = [];

    public function __construct(string $name)
    {
        $this->node_name = $name;
    }

    public function __toString(): string
    {
        $result = str_repeat("+",$this->node_index+1) . $this->node_name ." (" . count($this->childs) . ")\n";
        foreach($this->childs as $child)
        {
            $child->setNodeIndex($this->node_index+1);
            $result.= $child;
        };

        return $result;
    }
    
    public function getName(): string
    {
        return $this->node_name;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->childs;
    }

    public function addChild(Node $node): NodeInterface
    {
        $this->childs[] = $node;
        return $this;
    }

    public function getNodeIndex(): int
    {
        return $this->node_index;
    }

    public function setNodeIndex(int $nodeIndex)
    {
        $this->node_index = $nodeIndex;
    }
}

?>