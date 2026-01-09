    <?php

    interface Engine
    {
        public function car():string;
    }
    class Tata implements Engine{
        public function car(): string
        {
            // TODO: Implement car() method.
            return "Tata";
        }
    }
    class Swift implements Engine{
        public function car(): string
        {
            // TODO: Implement car() method.
            return "Swift";
        }
    }
    class BMW implements Engine{
        public function car(): string
        {
            echo "BMW";
            // TODO: Implement car() method.
            return "BMW";
        }
    }

    class Main{
        private $engine;
        public function __construct(Engine $engine){
            $this->engine = $engine;
        }
        public function car(){
            $car = $this->engine->car();
            echo $car;
        }
    }
    $main = new Main(new BMW());
    $main->car();


    interface Type{
        public  function setFilter(Filter $filter):void;
        public function getFilter():Filter;
    }
    {

    }
    interface  Reporting
    {
        public function report(Filter $filter):array;
    }

    interface Filter
    {
        public function filter():string;
    }

    class DealerReports implements Reporting{

        public function filter(): string
        {
            $dateTIme="2025-10-11";
            $state="UP";
            $city="UK";
            $type="Dealer";
            return $dateTIme.$state.$city.$type;
        }

        public function report($filter): array
        { ## there we are generate report useding the filter basic
            this->report($filter);
            return [];
        }
    }

    $delearReports=new DealerReports();
    $filters=$delearReports->filter();
    $arr=$delearReports->report($filters);

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Graph Theory – BFS & DFS</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            body{font-family:Inter,sans-serif;}
            h1,h2,h3{font-family:Merriweather,serif;}
        </style>
    </head>

    <body class="bg-[#0e0e0e]">

    <article class="w-full mx-auto bg-[#fbfbfb] px-10 py-14 my-16 rounded-3xl shadow-2xl">

        <h1 class="text-5xl font-black mb-6">Graph Theory – BFS & DFS Explained</h1>
        <p class="text-lg text-gray-700 leading-relaxed mb-12">
            Graph theory is the hidden language behind maps, social networks, routing systems and digital connections.
        </p>

        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-3">Introduction of Graph</h2>
            <p class="leading-relaxed text-gray-700">
                A graph is a non-linear data structure made up of nodes (vertices) and edges.
                It models cities, networks, maps and digital relationships.
            </p>
        </section>

        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-3">Important Components</h2>
            <ul class="list-disc pl-6 text-gray-700 leading-relaxed">
                <li><b>Node:</b> Represents cities, places or things</li>
                <li><b>Edge:</b> Connection between nodes</li>
                <li>Directed and Undirected nodes</li>
                <li>Weighted and Unweighted edges</li>
            </ul>
        </section>

        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-3">Types of Graph</h2>
            <ul class="list-disc pl-6 text-gray-700 leading-relaxed">
                <li>Directed Graph</li>
                <li>Undirected Graph</li>
                <li>Weighted Graph</li>
                <li>Unweighted Graph</li>
                <li>Cyclic Graph</li>
                <li>Acyclic Graph</li>
            </ul>
        </section>

        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-5">Graph Representations</h2>

            <div class="grid md:grid-cols-2 gap-8">

                <div class="border border-gray-300 p-6 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">Adjacency Matrix</h3>
                    <ul class="text-gray-700 list-disc pl-5">
                        <li>Memory: Heavy</li>
                        <li>Space: O(N²)</li>
                        <li>Edge Check: O(1)</li>
                        <li>Traversal: Slow</li>
                        <li>Scalability: Bad</li>
                        <li>Real Use: Rare</li>
                    </ul>
                </div>

                <div class="border border-gray-300 p-6 rounded-xl">
                    <h3 class="font-bold text-lg mb-2">Adjacency List</h3>
                    <ul class="text-gray-700 list-disc pl-5">
                        <li>Memory: Tight</li>
                        <li>Space: O(N + E)</li>
                        <li>Edge Check: O(deg(v))</li>
                        <li>Traversal: Fast</li>
                        <li>Scalability: Excellent</li>
                        <li>Real Use: Almost Always</li>
                    </ul>
                </div>

            </div>
        </section>

        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-3">BFS – Breadth First Search</h2>

            <p class="text-gray-700 leading-relaxed mb-3">
                <b>Definition:</b>
                Breadth First Search is a graph traversal technique that explores nodes level by level, starting from a given source node. It visits all immediate neighbors first before moving to the next level.
            </p>

            <p class="text-gray-700 leading-relaxed mb-3">
                <b>Technique:</b>
                BFS uses a Queue data structure. The source node is inserted into the queue and marked as visited. Then, each node is dequeued, its unvisited adjacent nodes are enqueued, and the process continues until the queue becomes empty.
            </p>

            <p class="text-gray-700 leading-relaxed">
                <b>Uses:</b>
            </p>
            <ul class="list-disc pl-6 text-gray-700">
                <li>Finding the shortest path in unweighted graphs</li>
                <li>Level order traversal of trees</li>
                <li>GPS and network routing systems</li>
                <li>Finding nearest neighbors in social networks</li>
            </ul>
        </section>


        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-3">DFS – Depth First Search</h2>

            <p class="text-gray-700 leading-relaxed mb-3">
                <b>Definition:</b>
                Depth First Search is a graph traversal technique that explores as far as possible along each branch before backtracking.
            </p>

            <p class="text-gray-700 leading-relaxed mb-3">
                <b>Technique:</b>
                DFS uses a Stack (explicitly or through recursion). The algorithm starts from a node, visits an unvisited adjacent node, and continues deep until no further nodes remain. Then it backtracks to explore other branches.
            </p>

            <p class="text-gray-700 leading-relaxed">
                <b>Uses:</b>
            </p>
            <ul class="list-disc pl-6 text-gray-700">
                <li>Maze and puzzle solving</li>
                <li>Cycle detection in graphs</li>
                <li>Topological sorting</li>
                <li>Finding connected components</li>
            </ul>
        </section>


        <section>
            <h2 class="text-2xl font-bold mb-4">BFS vs DFS</h2>
            <table class="w-full border border-gray-400 text-center text-gray-700">
                <tr class="bg-gray-100">
                    <th class="p-3">BFS</th>
                    <th class="p-3">DFS</th>
                </tr>
                <tr>
                    <td class="p-3">Level by level</td>
                    <td class="p-3">Deep traversal</td>
                </tr>
                <tr class="bg-gray-100">
                    <td class="p-3">Queue</td>
                    <td class="p-3">Stack / Recursion</td>
                </tr>
                <tr>
                    <td class="p-3">Shortest path</td>
                    <td class="p-3">All paths</td>
                </tr>
            </table>
        </section>

    </article>
    </body>

    </html>

    ishe template ka use karke UNION and Interactions in mysql , expain and write in html and tailwind css only
