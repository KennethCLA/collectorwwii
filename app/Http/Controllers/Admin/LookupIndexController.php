<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LookupIndexController extends Controller
{
    public function index(Request $request, string $type)
    {
        $config = $this->config($type);
        abort_unless(Schema::hasTable($config['table']), 404);
        $search = trim((string) $request->query('q', ''));

        $rows = DB::table($config['table'])
            ->select(['id', 'name', 'created_at'])
            ->when($this->hasSoftDeletes($config['table']), fn ($query) => $query->whereNull('deleted_at'))
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(100)
            ->withQueryString();

        $rows->getCollection()->transform(function ($row) use ($config) {
            $usage = $this->usageTotal($config['references'], (int) $row->id);
            $row->usage_total = $usage;

            return $row;
        });

        return view('admin.lookups.index', [
            'rows' => $rows,
            'type' => $type,
            'search' => $search,
            'label' => $config['label'],
            'description' => $config['description'],
        ]);
    }

    public function store(Request $request, string $type)
    {
        $config = $this->config($type);
        abort_unless(Schema::hasTable($config['table']), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim((string) $validated['name']);

        try {
            $payload = ['name' => $name];
            if (Schema::hasColumn($config['table'], 'created_at')) {
                $payload['created_at'] = now();
            }
            if (Schema::hasColumn($config['table'], 'updated_at')) {
                $payload['updated_at'] = now();
            }

            DB::table($config['table'])->insert($payload);
        } catch (UniqueConstraintViolationException) {
            // Ignore duplicates and continue with success response.
        } catch (\Illuminate\Database\QueryException) {
            // Also ignore duplicate-key QueryException variants.
        }

        return redirect()
            ->route('admin.lookups.index', ['type' => $type])
            ->with('success', "{$config['label']} updated.");
    }

    public function destroy(string $type, int $id)
    {
        $config = $this->config($type);
        $table = $config['table'];
        abort_unless(Schema::hasTable($table), 404);

        $query = DB::table($table)->where('id', $id);
        if ($this->hasSoftDeletes($table)) {
            $query->whereNull('deleted_at');
        }

        $row = $query->first();
        abort_if(! $row, 404);

        $usage = $this->usageTotal($config['references'], $id);
        if ($usage > 0) {
            return redirect()
                ->route('admin.lookups.index', ['type' => $type])
                ->with('error', "Cannot delete '{$row->name}': this option is in use.");
        }

        if ($this->hasSoftDeletes($table)) {
            $update = ['deleted_at' => now()];
            if (Schema::hasColumn($table, 'updated_at')) {
                $update['updated_at'] = now();
            }

            DB::table($table)
                ->where('id', $id)
                ->update($update);
        } else {
            DB::table($table)->where('id', $id)->delete();
        }

        return redirect()
            ->route('admin.lookups.index', ['type' => $type])
            ->with('success', "Deleted '{$row->name}'.");
    }

    private function hasSoftDeletes(string $table): bool
    {
        return Schema::hasColumn($table, 'deleted_at');
    }

    private function usageTotal(array $references, int $id): int
    {
        return collect($references)->sum(function (array $ref) use ($id) {
            if (! Schema::hasTable($ref['table']) || ! Schema::hasColumn($ref['table'], $ref['column'])) {
                return 0;
            }

            $query = DB::table($ref['table'])->where($ref['column'], $id);

            if ($this->hasSoftDeletes($ref['table'])) {
                $query->whereNull('deleted_at');
            }

            return $query->count();
        });
    }

    private function config(string $type): array
    {
        $map = [
            'book-topics' => [
                'label' => 'Book Topics',
                'description' => 'Options used in book topic fields.',
                'table' => 'book_topics',
                'references' => [
                    ['table' => 'books', 'column' => 'topic_id'],
                ],
            ],
            'origins' => [
                'label' => 'Origins',
                'description' => 'Shared options used by books and items.',
                'table' => 'origins',
                'references' => [
                    ['table' => 'books', 'column' => 'origin_id'],
                    ['table' => 'items', 'column' => 'origin_id'],
                ],
            ],
            'book-covers' => [
                'label' => 'Book Covers',
                'description' => 'Options used in book cover fields.',
                'table' => 'book_covers',
                'references' => [
                    ['table' => 'books', 'column' => 'cover_id'],
                ],
            ],
            'book-series' => [
                'label' => 'Book Series',
                'description' => 'Options used in book series fields.',
                'table' => 'book_series',
                'references' => [
                    ['table' => 'books', 'column' => 'series_id'],
                ],
            ],
            'locations' => [
                'label' => 'Locations',
                'description' => 'Storage locations used by collection entries.',
                'table' => 'locations',
                'references' => [
                    ['table' => 'books', 'column' => 'location_id'],
                    ['table' => 'banknotes', 'column' => 'location_id'],
                    ['table' => 'coins', 'column' => 'location_id'],
                    ['table' => 'postcards', 'column' => 'location_id'],
                    ['table' => 'stamps', 'column' => 'location_id'],
                ],
            ],
            'item-categories' => [
                'label' => 'Item Categories',
                'description' => 'Options used in item category fields.',
                'table' => 'item_categories',
                'references' => [
                    ['table' => 'items', 'column' => 'category_id'],
                ],
            ],
            'item-nationalities' => [
                'label' => 'Item Nationalities',
                'description' => 'Options used in item nationality fields.',
                'table' => 'item_nationalities',
                'references' => [
                    ['table' => 'items', 'column' => 'nationality_id'],
                ],
            ],
            'item-organizations' => [
                'label' => 'Item Organizations',
                'description' => 'Options used in item organization fields.',
                'table' => 'item_organizations',
                'references' => [
                    ['table' => 'items', 'column' => 'organization_id'],
                ],
            ],
            'countries' => [
                'label' => 'Countries',
                'description' => 'Shared country options used across sections.',
                'table' => 'countries',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'country_id'],
                    ['table' => 'coins', 'column' => 'country_id'],
                    ['table' => 'postcards', 'column' => 'country_id'],
                    ['table' => 'stamps', 'column' => 'country_id'],
                ],
            ],
            'currencies' => [
                'label' => 'Currencies',
                'description' => 'Shared currency options used across sections.',
                'table' => 'currencies',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'currency_id'],
                    ['table' => 'coins', 'column' => 'currency_id'],
                    ['table' => 'postcards', 'column' => 'currency_id'],
                    ['table' => 'stamps', 'column' => 'currency_id'],
                ],
            ],
            'nominal-values' => [
                'label' => 'Nominal Values',
                'description' => 'Shared nominal value options used across sections.',
                'table' => 'nominal_values',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'nominal_value_id'],
                    ['table' => 'coins', 'column' => 'nominal_value_id'],
                    ['table' => 'postcards', 'column' => 'nominal_value_id'],
                    ['table' => 'stamps', 'column' => 'nominal_value_id'],
                ],
            ],
            'banknote-series' => [
                'label' => 'Banknote Series',
                'description' => 'Options used in banknote series fields.',
                'table' => 'banknote_series',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'series_id'],
                ],
            ],
            'banknote-time-periods' => [
                'label' => 'Banknote Time Periods',
                'description' => 'Options used in banknote period fields.',
                'table' => 'banknote_time_periods',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'time_period_id'],
                ],
            ],
            'banknote-designers' => [
                'label' => 'Banknote Designers',
                'description' => 'Options used in banknote designer fields.',
                'table' => 'banknote_designers',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'designer_id'],
                ],
            ],
            'banknote-watermarks' => [
                'label' => 'Banknote Watermarks',
                'description' => 'Options used in banknote watermark fields.',
                'table' => 'banknote_watermarks',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'watermark_id'],
                ],
            ],
            'heads-of-state' => [
                'label' => 'Heads of State',
                'description' => 'Options used in banknote and coin fields.',
                'table' => 'heads_of_state',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'head_of_state_id'],
                    ['table' => 'coins', 'column' => 'head_of_state_id'],
                ],
            ],
            'colours' => [
                'label' => 'Colours',
                'description' => 'Options used in banknote, postcard and stamp fields.',
                'table' => 'colours',
                'references' => [
                    ['table' => 'banknotes', 'column' => 'colour_id'],
                    ['table' => 'postcards', 'column' => 'colour_id'],
                    ['table' => 'stamps', 'column' => 'colour_id'],
                ],
            ],
            'print-types' => [
                'label' => 'Print Types',
                'description' => 'Options used in postcard and stamp fields.',
                'table' => 'print_types',
                'references' => [
                    ['table' => 'postcards', 'column' => 'print_type_id'],
                    ['table' => 'stamps', 'column' => 'print_type_id'],
                ],
            ],
            'coin-shapes' => [
                'label' => 'Coin Shapes',
                'description' => 'Options used in coin shape fields.',
                'table' => 'coin_shapes',
                'references' => [
                    ['table' => 'coins', 'column' => 'shape_id'],
                ],
            ],
            'coin-materials' => [
                'label' => 'Coin Materials',
                'description' => 'Options used in coin material fields.',
                'table' => 'coin_materials',
                'references' => [
                    ['table' => 'coins', 'column' => 'material_id'],
                ],
            ],
            'coin-occasions' => [
                'label' => 'Coin Occasions',
                'description' => 'Options used in coin occasion fields.',
                'table' => 'coin_occasions',
                'references' => [
                    ['table' => 'coins', 'column' => 'occasion_id'],
                ],
            ],
            'coin-designers' => [
                'label' => 'Coin Designers',
                'description' => 'Options used in coin designer fields.',
                'table' => 'coin_designers',
                'references' => [
                    ['table' => 'coins', 'column' => 'designer_id'],
                ],
            ],
            'coin-strike-marks' => [
                'label' => 'Coin Strike Marks',
                'description' => 'Options used in coin strike mark fields.',
                'table' => 'coin_strike_marks',
                'references' => [
                    ['table' => 'coins', 'column' => 'strike_mark_id'],
                ],
            ],
            'coin-front-images' => [
                'label' => 'Coin Front Images',
                'description' => 'Options used in coin front image fields.',
                'table' => 'coin_front_images',
                'references' => [
                    ['table' => 'coins', 'column' => 'front_image_id'],
                ],
            ],
            'coin-front-texts' => [
                'label' => 'Coin Front Texts',
                'description' => 'Options used in coin front text fields.',
                'table' => 'coin_front_texts',
                'references' => [
                    ['table' => 'coins', 'column' => 'front_text_id'],
                ],
            ],
            'coin-reverse-images' => [
                'label' => 'Coin Reverse Images',
                'description' => 'Options used in coin reverse image fields.',
                'table' => 'coin_reverse_images',
                'references' => [
                    ['table' => 'coins', 'column' => 'reverse_image_id'],
                ],
            ],
            'coin-reverse-texts' => [
                'label' => 'Coin Reverse Texts',
                'description' => 'Options used in coin reverse text fields.',
                'table' => 'coin_reverse_texts',
                'references' => [
                    ['table' => 'coins', 'column' => 'reverse_text_id'],
                ],
            ],
            'coin-rims' => [
                'label' => 'Coin Rims',
                'description' => 'Options used in coin rim fields.',
                'table' => 'coin_rims',
                'references' => [
                    ['table' => 'coins', 'column' => 'rim_id'],
                ],
            ],
            'coin-rim-texts' => [
                'label' => 'Coin Rim Texts',
                'description' => 'Options used in coin rim text fields.',
                'table' => 'coin_rim_texts',
                'references' => [
                    ['table' => 'coins', 'column' => 'rim_text_id'],
                ],
            ],
            'postcard-types' => [
                'label' => 'Postcard Types',
                'description' => 'Options used in postcard type fields.',
                'table' => 'postcard_types',
                'references' => [
                    ['table' => 'postcards', 'column' => 'postcard_type_id'],
                ],
            ],
            'postcard-valuation-images' => [
                'label' => 'Postcard Valuation Images',
                'description' => 'Options used in postcard valuation image fields.',
                'table' => 'postcard_valuation_images',
                'references' => [
                    ['table' => 'postcards', 'column' => 'valuation_image_id'],
                ],
            ],
            'stamp-types' => [
                'label' => 'Stamp Types',
                'description' => 'Options used in stamp type fields.',
                'table' => 'stamp_types',
                'references' => [
                    ['table' => 'stamps', 'column' => 'type_id'],
                ],
            ],
            'stamp-designers' => [
                'label' => 'Stamp Designers',
                'description' => 'Options used in stamp designer fields.',
                'table' => 'stamp_designers',
                'references' => [
                    ['table' => 'stamps', 'column' => 'designer_id'],
                ],
            ],
            'stamp-watermarks' => [
                'label' => 'Stamp Watermarks',
                'description' => 'Options used in stamp watermark fields.',
                'table' => 'stamp_watermarks',
                'references' => [
                    ['table' => 'stamps', 'column' => 'watermark_id'],
                ],
            ],
            'stamp-gums' => [
                'label' => 'Stamp Gums',
                'description' => 'Options used in stamp gum fields.',
                'table' => 'stamp_gums',
                'references' => [
                    ['table' => 'stamps', 'column' => 'gum_id'],
                ],
            ],
            'stamp-perforations' => [
                'label' => 'Stamp Perforations',
                'description' => 'Options used in stamp perforation fields.',
                'table' => 'stamp_perforations',
                'references' => [
                    ['table' => 'stamps', 'column' => 'perforation_id'],
                ],
            ],
            'stamp-printing-houses' => [
                'label' => 'Stamp Printing Houses',
                'description' => 'Options used in stamp printing house fields.',
                'table' => 'stamp_printing_houses',
                'references' => [
                    ['table' => 'stamps', 'column' => 'printing_house_id'],
                ],
            ],
        ];

        abort_unless(isset($map[$type]), 404);

        return $map[$type];
    }
}
