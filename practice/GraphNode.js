/**
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

    directShortestPath(start, end) {
        if (!this.adjacentList.has(start) || !this.adjacentList.has(end)) {
            console.log("Start or End node does not exist");
            return null;
        }

        const visited = new Set();
        const parent = new Map();
        const queue = [];

        queue.push(start);
        visited.add(start);
        parent.set(start, null);

        while (queue.length) {
            const node = queue.shift();

            if (node === end) break;


            const neighbors = this.adjacentList.get(node).negibhor;

            for (let [to] of neighbors) {
                if (!visited.has(to)) {
                    visited.add(to);
                    parent.set(to, node);
                    queue.push(to);
                }
            }
        }

        if (!parent.has(end)) {
            console.log("No directed path found");
            return null;
        }

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
 **/
class GraphNode {
    constructor(data) {
        this.data = data;
        this.neighbor = new Map(); // outgoing edges
    }
}

class Graph {
    constructor() {
        this.adjacentList = new Map();
    }

    addNode(data) {
        if (!this.adjacentList.has(data)) {
            this.adjacentList.set(data, new GraphNode(data));
        }
    }

    // Directed edge: from -> to
    addDirectedEdge(from, to) {
        if (!this.adjacentList.has(from) || !this.adjacentList.has(to)) {
            console.log("Node missing");
            return;
        }
        this.adjacentList.get(from).neighbor.set(to, this.adjacentList.get(to));
    }

    printGraph() {
        console.log("---- DIRECTED GRAPH ----");
        for (let [key, node] of this.adjacentList) {
            console.log(`${key} -> ${[...node.neighbor.keys()].join(", ")}`);
        }
        console.log("------------------------");
    }

    // BFS shortest path
    directShortestPath(start, end) {
        if (!this.adjacentList.has(start) || !this.adjacentList.has(end)) {
            return null;
        }

        const visited = new Set();
        const parent = new Map();
        const queue = [];

        queue.push(start);
        visited.add(start);
        parent.set(start, null);

        while (queue.length) {
            const current = queue.shift();

            if (current === end) break;

            for (let [neighbor] of this.adjacentList.get(current).neighbor) {
                if (!visited.has(neighbor)) {
                    visited.add(neighbor);
                    parent.set(neighbor, current);
                    queue.push(neighbor);
                }
            }
        }

        if (!parent.has(end)) return null;

        const path = [];
        let cur = end;
        while (cur !== null) {
            path.push(cur);
            cur = parent.get(cur);
        }
        return path.reverse();
    }

    // ✅ BFS Traversal
    bfs(start) {
        if (!this.adjacentList.has(start)) return;

        const visited = new Set();
        const queue = [start];

        visited.add(start);

        while (queue.length) {
            const node = queue.shift();
            console.log(node);

            for (let [neighbor] of this.adjacentList.get(node).neighbor) {
                if (!visited.has(neighbor)) {
                    visited.add(neighbor);
                    queue.push(neighbor);
                }
            }
        }
    }

    // ✅ DFS Traversal
    dfs(start, visited = new Set()) {
        if (!this.adjacentList.has(start) || visited.has(start)) return;

        console.log(start);
        visited.add(start);

        for (let [neighbor] of this.adjacentList.get(start).neighbor) {
            this.dfs(neighbor, visited);
        }
    }
}
const graph = new Graph();

[1,2,3,4,5,6].forEach(n => graph.addNode(n));

graph.addDirectedEdge(1, 2);
graph.addDirectedEdge(2, 3);
graph.addDirectedEdge(3, 6);
graph.addDirectedEdge(1, 4);
graph.addDirectedEdge(4, 5);
graph.addDirectedEdge(5, 6);

graph.printGraph();

console.log("BFS from 1:");
graph.bfs(1);

console.log("DFS from 1:");
graph.dfs(1);

console.log("Shortest Path 1 -> 6 :", graph.directShortestPath(1, 6));

