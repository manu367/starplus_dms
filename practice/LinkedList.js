class DNode{
    constructor(data){
        this.data = data;
        this.prev = null;
        this.next = null;
    }
}

class DoublyLinkedList{
    constructor(){
        this.head = null;
        this.tail = null;
    }

    // O(1)
    addFirst(data){
        const node = new DNode(data);

        if(this.head === null){
            this.head = this.tail = node;
            return;
        }

        node.next = this.head;
        this.head.prev = node;
        this.head = node;
    }

    // O(1)
    addLast(data){
        const node = new DNode(data);

        if(this.head === null){
            this.head = this.tail = node;
            return;
        }

        node.prev = this.tail;
        this.tail.next = node;
        this.tail = node;
    }

    addLast1(){
        let tamp=this.head;
        if(this.head === null){
            this.head = this.head = node;
            return;
        }
        this.head = tamp;
        this.tail = tamp;
    }

    // O(n)
    addAt(index, data){
        if(index < 0) return;

        if(index === 0){
            this.addFirst(data);
            return;
        }

        let temp = this.head;
        let count = 0;

        while(temp !== null && count < index){
            temp = temp.next;
            count++;
        }

        if(temp === null){
            this.addLast(data);
            return;
        }

        const node = new DNode(data);

        node.prev = temp.prev;
        node.next = temp;
        temp.prev.next = node;
        temp.prev = node;
    }

    // O(1)
    deleteFirst(){
        if(this.head === null) return;

        if(this.head === this.tail){
            this.head = this.tail = null;
            return;
        }

        this.head = this.head.next;
        this.head.prev = null;
    }

    // O(1)
    deleteLast(){
        if(this.head === null) return;

        if(this.head === this.tail){
            this.head = this.tail = null;
            return;
        }

        this.tail = this.tail.prev;
        this.tail.next = null;
    }

    // O(n)
    deleteAt(index){
        if(index < 0 || this.head === null) return;

        if(index === 0){
            this.deleteFirst();
            return;
        }

        let temp = this.head;
        let count = 0;

        while(temp !== null && count < index){
            temp = temp.next;
            count++;
        }

        if(temp === null) return;

        if(temp === this.tail){
            this.deleteLast();
            return;
        }

        temp.prev.next = temp.next;
        temp.next.prev = temp.prev;
    }

    traverseForward(){
        let temp = this.head;
        let out = "";
        while(temp){
            out += temp.data + " ⇄ ";
            temp = temp.next;
        }
        console.log(out + "NULL");
    }

    traverseBackward(){
        let temp = this.tail;
        let out = "";
        while(temp){
            out += temp.data + " ⇄ ";
            temp = temp.prev;
        }
        console.log(out + "NULL");
    }

    reverse(){
        let curr = this.head;
        let temp = null;

        while(curr){
            temp = curr.prev;
            curr.prev = curr.next;
            curr.next = temp;
            curr = curr.prev;
        }

        temp = this.head;
        this.head = this.tail;
        this.tail = temp;
    }

    Searching(value){
        let temp = this.head;
        let index = 0;

        while(temp){
            if(temp.data === value) return index;
            temp = temp.next;
            index++;
        }
        return -1;
    }

    length(){
        let count = 0;
        let temp = this.head;
        while(temp){
            count++;
            temp = temp.next;
        }
        return count;
    }


}

const dll = new DoublyLinkedList();
dll.addLast(10);
dll.addLast(20);
dll.addLast(30);
dll.addFirst(5);
dll.addAt(2,99);

dll.traverseForward();   // 5 ⇄ 10 ⇄ 99 ⇄ 20 ⇄ 30 ⇄ NULL
dll.traverseBackward();  // 30 ⇄ 20 ⇄ 99 ⇄ 10 ⇄ 5 ⇄ NULL

