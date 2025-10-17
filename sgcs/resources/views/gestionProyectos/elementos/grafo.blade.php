
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Grafo de Trazabilidad de Elementos de Configuración
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Proyecto: <span class="font-mono font-bold">{{ $proyecto->nombre }}</span>
        </p>
    </x-slot>

    <div class="container py-8">
        <div id="ec-graph" style="height: 600px; border:1px solid #ccc;"></div>
    </div>
    <link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch("{{ route('proyectos.elementos.grafo', ['proyecto' => $proyecto->id]) }}")
                .then(response => response.json())
                .then(data => {
                    var container = document.getElementById('ec-graph');
                    var nodes = new vis.DataSet(data.nodes);
                    var edges = new vis.DataSet(data.edges);
                    var network = new vis.Network(container, { nodes, edges }, {
                        layout: { improvedLayout: true },
                        physics: { stabilization: true },
                        edges: { arrows: 'to', font: { align: 'middle' } },
                        nodes: { shape: 'box', font: { size: 16 } }
                    });
                });
        });
    </script>
</x-app-layout>
