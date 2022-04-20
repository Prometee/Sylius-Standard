<?php

declare(strict_types=1);

namespace App\Graphviz\Dumper;

class StateMachineGraphvizDumper extends GraphvizDumper
{
    /**
     * {@inheritdoc}
     *
     * Dumps the workflow as a graphviz graph.
     *
     * Available options:
     *
     *  * graph: The default options for the whole graph
     *  * node: The default options for nodes (places)
     *  * edge: The default options for edges
     */
    public function dump(array $config, array $marking = null, array $options = [])
    {
        $places = $this->findPlaces($config, $marking);
        $edges = $this->findEdges($config);
        $options = array_replace_recursive(self::$defaultOptions, $options);

        return $this->startDot($options)
            . $this->addPlaces([
                '_start_' => [
                    'attributes' => [
                        'shape' => 'point',
                        'style' => 'filled',
                        'fillcolor' => 'black',
                        'width' => '0.25',
                    ],
                ],
            ])
            . $this->addPlaces($places)
            . $this->addEdges([
                '_start_' => [
                    [
                        'name' => '',
                        'to' => $config['states'][0],
                        'attributes' => [],
                    ],
                ],
            ])
            . $this->addEdges($edges)
            . $this->endDot()
            ;
    }

    /**
     * @internal
     */
    protected function findEdges(array $config): array
    {
        $edges = [];
        foreach ($config['transitions'] as $transitionName => $transition) {
            $attributes = [];
            //$transitionName = $workflowMetadata->getMetadata('label', $transition) ?? $transitionName;
            /*$labelColor = $workflowMetadata->getMetadata('color', $transition);
            if (null !== $labelColor) {
                $attributes['fontcolor'] = $labelColor;
            }*/
            /*$arrowColor = $workflowMetadata->getMetadata('arrow_color', $transition);
            if (null !== $arrowColor) {
                $attributes['color'] = $arrowColor;
            }*/
            foreach ($transition['from'] as $from) {
                foreach ((array) $transition['to'] as $to) {
                    $attributes['style'] = 'solid';
                    $attributes['color'] = 'black';
                    if ($from === $to) {
                        $attributes['style'] = 'dashed';
                        $attributes['color'] = '#1abb9c';
                    }
                    $edge = [
                        'name' => $transitionName,
                        'to' => $to,
                        'attributes' => $attributes,
                    ];
                    $edges[$from][] = $edge;
                }
            }
        }

        return $edges;
    }

    /**
     * @internal
     */
    protected function addEdges(array $edges): string
    {
        $code = '';
        foreach ($edges as $id => $subEdges) {
            foreach ($subEdges as $edge) {
                $code .= sprintf(
                    "  place_%s -> place_%s [label=\"%s\", %s];\n",
                    $this->dotize($id),
                    $this->dotize($edge['to']),
                    $this->escape($edge['name']),
                    $this->addAttributes($edge['attributes'])
                );
            }
        }

        return $code;
    }
}
