class Node{
    constructor(data){
        this.data=data;
        this.node=[];
    }
}

class Graph{
    constructor(){
        this.adjacencyList=[];
    }
    addNode(data){
        const node=new Node(data);
        if(this.adjacencyList.length==0){
            this.adjacencyList.push(node);
        }
        else if (this.adjacencyList[data]){
            console.log("Node does already exist");
            return;
        }
        else{
            this.adjacencyList.push(node);
        }
    }
    remodeNode(data=''){
        if(data === undefined || data === null){
            return;
        }
        const index = this.adjacencyList.findIndex(n => n.data === data);
        if(index === -1){
            console.log("Node doesn't exist");
            return;
        }
        this.adjacencyList.splice(index, 1);
    }
    addEdge(a,b){
        const node1=this.adjacencyList.find(n => n.data === a);
        const node2=this.adjacencyList.find(n => n.data === b);
        if(!node1 || !node2){ console.log("Node doesn't exist");return;}
        node1.node.push(b);
        node2.node.push(a);

    }

    dfs(startData){
        const startNode = this.adjacencyList.find(n => n.data === startData);
        if(!startNode){
            console.log("Start node not found");
            return;
        }

        const visited = new Set();

        const travel = (node) => {
            console.log(node.data);
            visited.add(node.data);

            for(let neighbor of node.node){
                if(!visited.has(neighbor)){
                    const nextNode = this.adjacencyList.find(n => n.data === neighbor);
                    if(nextNode) travel(nextNode);
                }
            }
        };

        travel(startNode);
    }

    bfs(startData){
        const startNode=this.adjacencyList.find(n => n.data === startData); // index se node nikal liya
        if(!startNode){ // check kiya node h ya nhi
            console.log("Start node not found");
            return;
        }
        // 2 condiotions : set: user for unique element and user queue for visted element
        const visited=new Set();
        const queue=[];
        queue.push(startNode);
        visited.add(startNode.data);

        // queue check karkene ke sabi element ko visite karne ke liye
        while(queue.length){
            const current=queue.shift(); // first element ko niakl de
            console.log(current.data);  // print those data
            for(const neighbor of current.node){ // attach node iterate kr rhe h
                if(!visited.has(neighbor)){
                    visited.add(neighbor);
                    const nextNode = this.adjacencyList.find(n => n.data === neighbor);
                    if(nextNode) queue.push(nextNode);
                }
            }
        }
    }
    shortestPath(startData, endData){
        const startNode = this.adjacencyList.find(n => n.data === startData);
        const endNode   = this.adjacencyList.find(n => n.data === endData);

        if(!startNode || !endNode){
            console.log("Start or End node not found");
            return;
        }

        const visited = new Set();
        const queue = [];
        const parent = {};   // breadcrumb trail

        queue.push(startNode);
        visited.add(startNode.data);
        parent[startNode.data] = null;

        while(queue.length){
            const current = queue.shift();

            if(current.data === endData) break;

            for(const neighbor of current.node){
                if(!visited.has(neighbor)){
                    visited.add(neighbor);
                    parent[neighbor] = current.data;
                    const nextNode = this.adjacencyList.find(n => n.data === neighbor);
                    if(nextNode) queue.push(nextNode);
                }
            }
        }

        // Rebuild path
        const path = [];
        let step = endData;

        while(step !== null && step !== undefined){
            path.push(step);
            step = parent[step];
        }

        path.reverse();

        if(path[0] !== startData){
            console.log("No path exists");
            return;
        }

        console.log(`Shortest Path (${path.length - 1} edges):`);
        console.log(path.join(" ➜ "));
        return path;
    }

}

const graph = new Graph();
graph.addNode("Delhi");
graph.addNode("Gurugoan");
graph.addNode("Faridabad");
graph.addNode("Noida");
graph.addNode("greater Noida");
graph.addNode("Ghaziyabad");
graph.addNode("dadri");
graph.addNode("bulandshar");
graph.addNode("Anpushar");
graph.addNode("bahjoi");
graph.addNode("Chandausi");
graph.addNode("hapur");
graph.addNode("garh");
graph.addNode("gajrola");
graph.addNode("Sambhal");
graph.addNode("moradabhad");
graph.addNode("bilari");

graph.addEdge("Delhi","Gurugoan");
graph.addEdge("Delhi","Noida");
graph.addEdge("Gurugoan","Faridabad");
graph.addEdge("Faridabad","Delhi");
graph.addEdge("Noida","Ghaziyabad");
graph.addEdge("Noida","greater Noida");

graph.addEdge("greater Noida","dadri");
graph.addEdge("dadri","bulandshar");
graph.addEdge("bulandshar","Anpushar");
graph.addEdge("Anpushar","bahjoi");
graph.addEdge("bahjoi","Chandausi");


//ghaziyabad
graph.addEdge("Ghaziyabad","hapur");
graph.addEdge("hapur","garh");
graph.addEdge("garh","gajrola");
graph.addEdge("gajrola","Sambhal");
graph.addEdge("Sambhal","Chandausi");
graph.addEdge("gajrola","moradabhad");
graph.addEdge("moradabhad","bilari");
graph.addEdge("bilari","Chandausi");

graph.shortestPath("greater Noida","Chandausi")
