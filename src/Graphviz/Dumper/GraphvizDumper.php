<?php

declare(strict_types=1);

namespace App\Graphviz\Dumper;

/**
 * GraphvizDumper dumps a workflow as a graphviz file.
 *
 * You can convert the generated dot file with the dot utility (https://graphviz.org/):
 *
 *   dot -Tpng workflow.dot > workflow.png
 */
class GraphvizDumper implements DumperInterface
{
    protected static array $defaultOptions = [
        'graph' => ['ratio' => 'compress', 'rankdir' => 'LR'],
        'node' => ['fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'fillcolor' => 'lightblue', 'fixedsize' => 'false', 'width' => 1, 'shape' => 'circle', 'style' => 'solid'],
        'edge' => ['fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'arrowhead' => 'normal', 'arrowsize' => 0.5, 'style' => 'solid'],
    ];

    /**
     * Dumps the workflow as a graphviz graph.
     *
     * Available options:
     *
     *  * graph: The default options for the whole graph
     *  * node: The default options for nodes (places + transitions)
     *  * edge: The default options for edges
     *
     *
     * @return string
     */
    public function dump(array $config, array $marking = null, array $options = [])
    {
        $places = $this->findPlaces($config, $marking);
        $transitions = $this->findTransitions($config);
        $edges = $this->findEdges($config);
        $options = array_replace_recursive(self::$defaultOptions, $options);

        return $this->startDot($options)
            . $this->addPlaces($places)
            . $this->addTransitions($transitions)
            . $this->addEdges($edges)
            . $this->endDot();
    }

    protected function findPlaces(array $config, array $marking = null): array
    {
        $places = [];
        $states = $config['states'];
        foreach ($states as $i => $place) {
            $attributes = [];
            if (in_array($place, (array) $states[0], true)) {
                $attributes['style'] = 'filled';
            }
            if ($marking && isset($marking[$place])) {
                $attributes['color'] = '#FF0000';
                $attributes['shape'] = 'doublecircle';
            }
            /*$backgroundColor = $workflowMetadata->getMetadata('bg_color', $place);
            if (null !== $backgroundColor) {
                $attributes['style'] = 'filled';
                $attributes['fillcolor'] = $backgroundColor;
            }
            $label = $workflowMetadata->getMetadata('label', $place);
            if (null !== $label) {
                $attributes['name'] = $label;
            }*/
            $places[$place] = [
                'attributes' => $attributes,
            ];
        }

        return $places;
    }

    protected function findTransitions(array $config): array
    {
        $transitions = [];
        foreach ($config['transitions'] as $transitionName => $transition) {
            $attributes = ['shape' => 'box', 'regular' => true];
            /*$backgroundColor = $workflowMetadata->getMetadata('bg_color', $transition);
            if (null !== $backgroundColor) {
                $attributes['style'] = 'filled';
                $attributes['fillcolor'] = $backgroundColor;
            }*/
            // $transitionName = $workflowMetadata->getMetadata('label', $transition) ?? $transitionName;
            $transitions[] = [
                'attributes' => $attributes,
                'name' => $transitionName,
            ];
        }

        return $transitions;
    }

    protected function addPlaces(array $places): string
    {
        $code = '';
        foreach ($places as $id => $place) {
            if (isset($place['attributes']['name'])) {
                $placeName = $place['attributes']['name'];
                unset($place['attributes']['name']);
            } else {
                $placeName = $id;
            }
            $code .= sprintf("  place_%s [label=\"%s\",%s];\n", $this->dotize((string) $id), $this->escape($placeName), $this->addAttributes($place['attributes']));
        }

        return $code;
    }

    protected function addTransitions(array $transitions): string
    {
        $code = '';
        foreach ($transitions as $i => $place) {
            $code .= sprintf("  transition_%s [label=\"%s\",%s];\n", $this->dotize((string) $i), $this->escape($place['name']), $this->addAttributes($place['attributes']));
        }

        return $code;
    }

    protected function findEdges(array $config): array
    {
        $dotEdges = [];
        $i = 0;
        foreach ($config['transitions'] as $transitionName => $transition) {
            // $transitionName = $workflowMetadata->getMetadata('label', $transition) ?? $transitionName;
            foreach ($transition['from'] as $from) {
                $dotEdges[] = [
                    'from' => $from,
                    'to' => $transitionName,
                    'direction' => 'from',
                    'transition_number' => $i,
                ];
            }
            foreach ((array) $transition['to'] as $to) {
                $dotEdges[] = [
                    'from' => $transitionName,
                    'to' => $to,
                    'direction' => 'to',
                    'transition_number' => $i,
                ];
            }
            ++$i;
        }

        return $dotEdges;
    }

    protected function addEdges(array $edges): string
    {
        $code = '';
        foreach ($edges as $edge) {
            if ('from' === $edge['direction']) {
                $code .= sprintf(
                    "  place_%s -> transition_%s [style=\"solid\"];\n",
                    $this->dotize($edge['from']),
                    $this->dotize((string) $edge['transition_number'])
                );
            } else {
                $code .= sprintf(
                    "  transition_%s -> place_%s [style=\"solid\"];\n",
                    $this->dotize((string) $edge['transition_number']),
                    $this->dotize($edge['to'])
                );
            }
        }

        return $code;
    }

    protected function startDot(array $options): string
    {
        return sprintf(
            "digraph workflow {\n  %s\n  node [%s];\n  edge [%s];\n\n",
            $this->addOptions($options['graph']),
            $this->addOptions($options['node']),
            $this->addOptions($options['edge'])
        );
    }

    protected function endDot(): string
    {
        return "}\n";
    }

    protected function dotize(string $id): string
    {
        return hash('sha1', $id);
    }

    /**
     * @param bool|string $value
     */
    protected function escape($value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return addslashes($value);
    }

    protected function addAttributes(array $attributes): string
    {
        $code = [];
        foreach ($attributes as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $this->escape($v));
        }

        return $code ? ' ' . implode(' ', $code) : '';
    }

    private function addOptions(array $options): string
    {
        $code = [];
        foreach ($options as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $v);
        }

        return implode(' ', $code);
    }
}
