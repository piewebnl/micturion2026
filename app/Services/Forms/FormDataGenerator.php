<?php

namespace App\Services\Forms;

use Illuminate\Database\Eloquent\Collection;

// Generates formdata (label and value) from any given elequont collection
class FormDataGenerator
{
    private $value = 'id'; // which field holds value

    private $label = ['name']; // which field holds label

    private $meta = []; // Extra info to pass through

    public function setValue(string $field): void
    {
        $this->value = $field;
    }

    public function setLabel(array $fields): void
    {
        $this->label = $fields;
    }

    public function setMeta(array $fields): void
    {
        $this->meta = $fields;
    }

    public function generateFilament(Collection $items): array
    {
        $formData = [];

        foreach ($items as $item) {
            $fullLabel = '';

            foreach ($this->label as $label) {
                // Relationship?
                if (str_contains($label, '.')) {
                    $relationship = explode('.', $label);
                    $fullLabel .= $item->{$relationship[0]}->{$relationship[1]} . ' - ';

                    continue;
                }

                $fullLabel .= $item->{$label} . ' - ';
            }

            $fullLabel = rtrim($fullLabel, ' - ');
            $formData[$item->{$this->value}] = $fullLabel;
        }

        return $formData;
    }

    public function generate(Collection $items): array
    {

        $formData = [];

        foreach ($items as $item) {
            $fullLabel = '';
            $fullMeta = [];

            foreach ($this->label as $label) {
                // Relationship?
                if (str_contains($label, '.')) {
                    $relationship = explode('.', $label);
                    $fullLabel .= $item->{$relationship[0]}->{$relationship[1]} . ' - ';

                    continue;
                }

                $fullLabel .= $item->{$label} . ' - ';
            }

            foreach ($this->meta as $meta) {
                $fullMeta[$meta] = $item->{$meta};
            }
            $fullLabel = rtrim($fullLabel, ' - ');

            $formData[] = [
                'value' => $item->{$this->value},
                'label' => $fullLabel,
                'meta' => $fullMeta,
            ];
        }

        return $formData;
    }
}
