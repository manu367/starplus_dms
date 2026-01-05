class GraphNode {
    constructor(data) {
        this.data=data;
        this.negibhor=new Map();
    }
}

class Graph {
    constructor(data) {
        this.adjacentList = new Map();
    }

    addNode(data){
        if(this.adjacentList.has(data)){
            console.log("data already exists");
            return;
        }
        this.adjacentList.set(data,new GraphNode(data));
        console.log("node is added");
    }
    // undirected graph
    addEdge(node1,node2){
        if(node1===node2){
            console.log("Self loop is not valid");
            return;
        }
        if(!this.adjacentList.has(node1)){
            console.log("Node1 not exists");
        }
        if(!this.adjacentList.has(node2)){
            console.log("Node2 not exists");
        }
        const n1=this.adjacentList.get(node1);
        const n2=this.adjacentList.get(node2)
        if (n1.negibhor.has(node2)) {
            console.log("Edge already exists between", node1, "and", node2);
            return;
        }
        n1.negibhor.set(node2, n2);
        n2.negibhor.set(node1, n1);
        console.log(`Edge added between ${node1} and ${node2}`);
    }
    printNode() {
        console.log("---- GRAPH STRUCTURE ----");
        for (let [key, node] of this.adjacentList) {
            const neighbors = [...node.negibhor.keys()];
            console.log(`${key} -> ${neighbors.join(", ")}`);
        }
        console.log("-------------------------");
    }

    shortestPath(start,end){
        if(!this.adjacentList.has(start)||!this.adjacentList.has(end)){
            console.log(`Starting ${start} point and Ending ${end} Point is not Exists`)
            return;
        }
        const visited = new Set();
        const parent=new Map();
        const queue=[];

        // add first souce
        queue.push(start);
        visited.add(start);
        parent.set(start,null);

        while(queue.length){
            const node=queue.shift(); // first removed
            if(node===end)break;
            const negibhor=this.adjacentList.get(node).negibhor;
            for(let [keys] of negibhor){
                if(!visited.has(keys)){
                    visited.add(keys);
                    parent.set(keys,node);
                    queue.push(keys);
                }
            }
        }
        if (!parent.has(end)) {
            console.log("No path found");
            return null;
        }
        // reconstruct path
        const path = [];
        let curr = end;
        while (curr !== null) {
            path.push(curr);
            curr = parent.get(curr);
        }

        return path.reverse();

    }
}
console.time("graph-test");
const graph = new Graph();
graph.addNode(12);
graph.addNode(25);
graph.addNode(26);
graph.addNode(38);
graph.addNode(50);
graph.addNode(40);

graph.addEdge(12,25);
graph.addEdge(12,26);
graph.addEdge(25,26);

graph.addEdge(26,40);
graph.addEdge(40,50);
graph.addEdge(50,38);

graph.printNode();
console.log(graph.shortestPath(12,38));
