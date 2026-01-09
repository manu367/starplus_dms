class Node1{
    constructor(data){
        this.data = data;
        this.left = null;
        this.right = null;
    }
}

class TreeNode{
    constructor(){
        this.root = null;
    }

    // INSERT
    addNode(data){
        const node = new Node1(data);

        if(this.root === null){
            this.root = node;
            return;
        }

        let current = this.root;

        while(true){
            if(data < current.data){
                if(current.left === null){
                    current.left = node;
                    return;
                }
                current = current.left;
            }
            else if(data > current.data){
                if(current.right === null){
                    current.right = node;
                    return;
                }
                current = current.right;
            }
            else{
                return; // no duplicates
            }
        }
    }

    // SEARCH
    findNode(data){
        let current = this.root;

        while(current){
            if(data === current.data) return true;
            if(data < current.data) current = current.left;
            else current = current.right;
        }
        return false;
    }

    // DELETE
    deleteNode(data){
        this.root = this._delete(this.root, data);
    }

    _delete(root, data){
        if(root === null) return root;

        if(data < root.data){
            root.left = this._delete(root.left, data);
        }
        else if(data > root.data){
            root.right = this._delete(root.right, data);
        }
        else{
            // No child
            if(root.left === null && root.right === null) return null;

            // One child
            if(root.left === null) return root.right;
            if(root.right === null) return root.left;

            // Two children
            let min = this._findMin(root.right);
            root.data = min;
            root.right = this._delete(root.right, min);
        }
        return root;
    }

    // UPDATE
    updateNode(oldVal, newVal){
        this.deleteNode(oldVal);
        this.addNode(newVal);
    }

    // TRAVERSALS
    inorder(){
        this._inorder(this.root);
    }
    _inorder(node){
        if(node){
            this._inorder(node.left);
            console.log(node.data);
            this._inorder(node.right);
        }
    }

    preorder(){
        this._preorder(this.root);
    }
    _preorder(node){
        if(node){
            console.log(node.data);
            this._preorder(node.left);
            this._preorder(node.right);
        }
    }

    postorder(){
        this._postorder(this.root);
    }
    _postorder(node){
        if(node){
            this._postorder(node.left);
            this._postorder(node.right);
            console.log(node.data);
        }
    }

    levelOrder(){
        if(!this.root) return;

        const queue = [this.root];

        while(queue.length){
            let node = queue.shift();
            console.log(node.data);

            if(node.left) queue.push(node.left);
            if(node.right) queue.push(node.right);
        }
    }

    // HEIGHT
    height(node=this.root){
        if(node === null) return -1;
        return 1 + Math.max(this.height(node.left), this.height(node.right));
    }

    // MIN & MAX
    findMin(){
        return this._findMin(this.root);
    }
    _findMin(node){
        while(node.left) node = node.left;
        return node.data;
    }

    findMax(){
        let node = this.root;
        while(node.right) node = node.right;
        return node.data;
    }

    // COUNT
    count(node=this.root){
        if(!node) return 0;
        return 1 + this.count(node.left) + this.count(node.right);
    }

    // BALANCE CHECK
    isBalanced(node=this.root){
        if(!node) return true;
        let lh = this.height(node.left);
        let rh = this.height(node.right);
        return Math.abs(lh - rh) <= 1 &&
            this.isBalanced(node.left) &&
            this.isBalanced(node.right);
    }
}
const tree = new TreeNode();

tree.addNode(50);
tree.addNode(30);
tree.addNode(40);
tree.addNode(0);
tree.addNode(10);
tree.addNode(12);
tree.addNode(32230);
tree.addNode(70);
tree.addNode(20);
tree.addNode(40);
tree.addNode(60);
tree.addNode(90);

tree.inorder();   // sorted order
console.log("Height:", tree.height());
console.log("Min:", tree.findMin());
console.log("Max:", tree.findMax());
console.log("Count:", tree.count());
console.log("Balanced:", tree.isBalanced());
