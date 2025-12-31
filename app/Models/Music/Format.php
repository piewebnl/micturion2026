<?php

namespace App\Models\Music;

use App\Traits\QueryCache\QueryCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Format extends Model
{
    use QueryCache;

    protected $guarded = [];

    // Filters to keep (or combinations 2xCD, 3xLP) -> config???
    private $filterFormatWhitelist = [
        'CD',
        'DVD',
        '12Inch',
        '10Inch',
        '7Inch',
        'CAS',
        'LP',
        'CDR',
        'VHS',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_formats');
    }

    public function storeFormats(array $formats): array
    {
        $ids = [];

        foreach ($formats as $format) {

            $formatModel = Format::firstOrNew(
                [
                    'name' => $format['name'],
                ],
                [
                    'parent_id' => null,
                ]
            );

            $formatModel->save();
            array_push($ids, $formatModel->id);

            // Subformat?
            if ($format['sub_format'] != null) {
                $formatModel = Format::firstOrNew(
                    [
                        'name' => $format['sub_format'],
                    ],
                    [
                        'parent_id' => $formatModel->id,
                    ]
                );
                $formatModel->save();
                array_push($ids, $formatModel->id);
            }
        }
        $ids = array_unique($ids);

        return $ids;
    }

    public function getFormatsByGrouping(?string $grouping): array
    {

        $grouping = preg_replace('/\((.*?)\)/', '', $grouping); // Remove Category (Albums)
        $grouping = str_replace('-', '+', $grouping);
        $formats = explode('+', $grouping);

        // generate pattern
        $pattern = "/\b(";
        foreach ($this->filterFormatWhitelist as $key => $word) {
            $pattern .= '(?:[0-9]x)?' . $word . '|';
        }
        $pattern = rtrim($pattern, '|');
        $pattern .= ")\b/i";

        $i = 0;
        $keep = [];

        foreach ($formats as $format) {

            $filterFormat = $this->filterFormat($format, $pattern); // Remove stuff but keep discs

            if ($filterFormat != '') {
                $keep[$i]['sub_format'] = $filterFormat;
                $keep[$i]['name'] = $this->removeNumberOfDiscs($filterFormat);
                // $keep[$i]['is_video'] = $this->isVideo($keep[$i]['name']);
                $i++;
            }
        }

        if (empty($keep)) {
            $keep[0]['sub_format'] = '';
            $keep[0]['name'] = 'None';
            // $keep[0]['is_video'] = false;
        }

        return $keep;
    }

    // Only keep certain formats
    private function filterFormat($format, $pattern)
    {
        preg_match($pattern, $format, $matches);
        if (isset($matches[0]) && $matches[0] != '') {
            return $matches[0];
        }
    }

    private function removeNumberOfDiscs($format)
    {
        $part = explode('x', $format);
        if (is_numeric($part[0])) {
            $format = $part[1];
        }

        return $format;
    }

    public function getAllFormats()
    {

        $formats = $this->getCache('get-all-formats');

        if (!$formats) {
            $formats = Format::where('parent_id', null)->orderBy('name')->get();
            $this->setCache('get-all-formats', [], $formats);
        }

        return $formats;
    }

    public function getFormatByName(array $names): array
    {
        $ids = DB::table('formats')
            ->whereIn('name', $names)
            ->orWhereIn('parent_id', function ($query) use ($names) {
                $query->select('id')
                    ->from('formats')
                    ->whereIn('name', $names);
            })
            ->pluck('id')
            ->unique()
            ->toArray();

        return $ids;
    }
}
