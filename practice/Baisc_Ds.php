<?php
class Node {
    public $data;
    public $left = null;  // Renamed from $node to follow tree conventions
    public $right = null;

    public function __construct($data) {
        $this->data = $data;
    }
}

class TreeNode {
    public $root = null;
    public function Add($data) {
        $newNode = new Node($data);

        if ($this->root === null) {
            $this->root = $newNode;
        } else {
            $this->insertNode($this->root, $newNode);
        }
    }

    private function insertNode($current, $newNode) {
        if ($newNode->data < $current->data) {
            if ($current->left === null) {
                $current->left = $newNode;
            } else {
                $this->insertNode($current->left, $newNode);
            }
        } else {
            if ($current->right === null) {
                $current->right = $newNode;
            } else {
                $this->insertNode($current->right, $newNode);
            }
        }
    }

    public function print(){
        if($this->root === null){
            echo "Empty Node";
        }
        $tem=$this->root;
        while($this->root !== null){
            echo $tem->data;
            $tem=$tem->right;
        }
    }
}
 $a=new TreeNode();
$a->Add(12);
$a->Add(13);

?>