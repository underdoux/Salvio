@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Dependency Graph for {{ $rule->name }}</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('commission-rules.dependencies.index', $rule) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Dependencies
                        </a>
                    </div>
                </div>

                <!-- Graph Controls -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex gap-4">
                        <button id="zoomIn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Zoom In
                        </button>
                        <button id="zoomOut" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Zoom Out
                        </button>
                        <button id="resetZoom" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Reset View
                        </button>
                    </div>
                </div>

                <!-- Graph Legend -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-2">Legend</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                            <span>Active Rule</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-red-500 mr-2"></div>
                            <span>Inactive Rule</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-1 bg-blue-500 mr-2"></div>
                            <span>Requires</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-1 bg-red-500 mr-2"></div>
                            <span>Conflicts</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-1 bg-purple-500 mr-2"></div>
                            <span>Overrides</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 border-2 border-yellow-500 mr-2"></div>
                            <span>Current Rule</span>
                        </div>
                    </div>
                </div>

                <!-- Graph Container -->
                <div id="graph-container" class="border rounded-lg" style="height: 600px;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis-network/9.1.2/vis-network.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data
    const nodes = new vis.DataSet(@json($nodes->map(function($node) use ($rule) {
        return [
            'id' => $node->id,
            'label' => $node->name,
            'color' => [
                'background' => $node->isActive() ? '#10B981' : '#EF4444',
                'border' => $node->id === $rule->id ? '#EAB308' : '#000000',
            ],
            'borderWidth' => $node->id === $rule->id ? 3 : 1,
        ];
    })));

    const edges = new vis.DataSet(@json($edges->map(function($edge) {
        return [
            'from' => $edge['from'],
            'to' => $edge['to'],
            'arrows' => 'to',
            'color' => [
                'color' => match($edge['type']) {
                    'requires' => '#3B82F6',
                    'conflicts' => '#EF4444',
                    'overrides' => '#8B5CF6',
                    default => '#000000',
                },
            ],
            'label' => ucfirst($edge['type']),
        ];
    })));

    // Create network
    const container = document.getElementById('graph-container');
    const data = { nodes, edges };
    const options = {
        nodes: {
            shape: 'circle',
            size: 30,
            font: {
                size: 14,
            },
        },
        edges: {
            font: {
                size: 12,
                align: 'middle',
            },
            smooth: {
                type: 'cubicBezier',
            },
        },
        physics: {
            enabled: true,
            barnesHut: {
                gravitationalConstant: -2000,
                centralGravity: 0.3,
                springLength: 200,
            },
        },
        interaction: {
            hover: true,
            tooltipDelay: 200,
        },
    };

    const network = new vis.Network(container, data, options);

    // Zoom controls
    document.getElementById('zoomIn').addEventListener('click', function() {
        network.moveTo({
            scale: network.getScale() * 1.2,
        });
    });

    document.getElementById('zoomOut').addEventListener('click', function() {
        network.moveTo({
            scale: network.getScale() / 1.2,
        });
    });

    document.getElementById('resetZoom').addEventListener('click', function() {
        network.fit({
            animation: {
                duration: 1000,
                easingFunction: 'easeInOutQuad',
            },
        });
    });

    // Initial fit
    network.once('stabilized', function() {
        network.fit();
    });

    // Double click to go to rule
    network.on('doubleClick', function(params) {
        if (params.nodes.length > 0) {
            const nodeId = params.nodes[0];
            if (nodeId !== @json($rule->id)) {
                window.location.href = `/commission-rules/${nodeId}/dependencies`;
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
#graph-container {
    background-color: #ffffff;
    cursor: grab;
}
#graph-container:active {
    cursor: grabbing;
}
</style>
@endpush
@endsection
